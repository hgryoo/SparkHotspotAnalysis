
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._

object test5 {
  
  def main(args: Array[String]) = {
    val degree= args(0).toDouble
    val time_step = args(1).toInt
    
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
    
    val conf = new SparkConf().
    setAppName("test5")
    .setMaster("local");
    
    val sc = new SparkContext(conf);
    
    val input = sc.textFile("file:///usr/local/sparkhotspot/yellow_tripdata_2015-01.csv");
    
    val data = sc.parallelize(input.take(10000)).map(line => line.split(",").map(elem => elem.trim))
    
    //val data = input.map(line => line.split(",").map(elem => elem.trim))

    
    val result2 = data.filter(line => line(0) != "VendorID").filter(line => line(5) != "0").filter(line => line(6) != 0)
    
    val result3 = result2.map( line => ( line(5), line(6), line(1) ) )
    
   
    
    val result4 = result3.map( line => ( ( (line._1.toDouble/degree).toInt ,
        (line._2.toDouble/degree).toInt,
        ( line._3.split(' ')(0).split('-')(2).toInt) / time_step ), 1) )
        
    
    val result4_p = result4.partitionBy(new HashPartitioner(100)).persist()
    val result5 = result4_p.reduceByKey( (x,y) => x + y)
    
    result5.persist(StorageLevel.DISK_ONLY)
     
    val rdd_size = result5.count();
    
    
    val mean = (result3.count()-1).toDouble /rdd_size.toDouble; // 
   
    val pow_sum = sc.accumulator(0);
    result5.foreach(line => {
      pow_sum += line._2 * line._2
    }
          
    )
    
    val pow_sum_mean = pow_sum.value.toDouble / rdd_size.toDouble;
    
    val S =  scala.math.sqrt(abs(pow_sum_mean - mean * mean));

    val weight_map = sc.accumulableCollection(scala.collection.mutable.HashMap[(Int , Int , Int), (Double,Double,Double)]());
     
    val broad_map = sc.broadcast(result5.collectAsMap());
    
     val find_weight = (line : ((Int,Int,Int),Int) ) => {
      var sum_weight_value = 0.0; //weight x value
      var sum_weight = 0.0;// 
      var sum_pow_weight = 0.0 ; // 
      broad_map.value.foreach{ target =>
        var dis = List[Int]();
            if (line._1._1-target._1._1 >0)
              dis = line._1._1-target._1._1 :: dis;
            else
              dis = -line._1._1.toInt+target._1._1.toInt :: dis;
            
            if (line._1._2 - target._1._2 > 0)
              dis = line._1._2 - target._1._2 :: dis
            else
              dis = - line._1._2 + target._1._2 :: dis
            
            if (line._1._3 - target._1._3 > 0 )
              dis = line._1._3 - target._1._3 :: dis;
            else
              dis = -line._1._3 + target._1._3 :: dis;
           
            
            var sort_list = dis.sortWith(_>_);
            
            var weight = 1.0 / pow(2,sort_list(0));
            
            sum_weight_value += weight * target._2;
            sum_weight += weight;
            sum_pow_weight += weight * weight;
      }
      (sum_weight_value,sum_weight,sum_pow_weight)
    }
     
    
    val g_rdd = result5.map { line =>
     
      val res = find_weight(line)
     // val res = (0.1,0.1,0.1)
    
     // weight_map += ((line._1._1,line._1._2,line._1._3) -> (res._1,res._2,res._3))
      val sum_weight_value = res._1
      val sum_weight =   res._2
      val sum_pow_weight =  res._3
      
      val denominator = S * sqrt((rdd_size * sum_pow_weight - sum_weight * sum_weight)/(rdd_size + 1));
      val molecule = sum_weight_value - mean * sum_weight;
      val g_value = molecule / denominator;
      
        ((line._1._1,line._1._2,line._1._3),g_value);
    }
    
   
  
   
     
    
    
    

    println("g_rdd")
  //  g_rdd.top(50).foreach(println)
  //  val hotspot = sc.parallelize(g_rdd.top())
    println("ver1.0 _ done!!!!!")
    
    
  //  hotspot.saveAsTextFile("file:///usr/local/sparkhotspot/test3/result.txt");
    //val g_map = 
  }
}
