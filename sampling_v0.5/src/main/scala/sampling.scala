
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._
import java.io._
import java.lang.System._

object sampling {
 def find_days(para : Int) : Int = {
	var x = para
	var days = 0
	if (para == 1)
	{ (0) }
	else{
		for (x <- para to 2){
			if (x == 2 | x == 4 | x == 6 | x == 8 | x == 9 | x == 11){
				days += 31;
			}else if (x == 3){
				days += 28;
			}
			else{
				days += 30;
			}
		}
	(days)
	}
  }

  def main(args: Array[String])  {


	val time_start = java.lang.System.currentTimeMillis();
	val degree= args(0).toDouble
	val time_step = args(1).toInt
	val heuristic = args(2).toInt
	val input_path = args(3)
	val result_path = args(4)
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
	val PART = 100
    
    val conf = new SparkConf().
    setAppName("sampling")
    .set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(Array(
	classOf[Array[String]],
	sampling.getClass,
	classOf[scala.collection.mutable.WrappedArray$ofRef]
	))

    val sc = new SparkContext(conf)
 	
   
    val input = sc.textFile(input_path)
    
    val data = input.map(line => line.split(",").map(elem => elem.trim))

    val result_filter = data.filter(line => line(0) != "VendorID").filter(line => line(5) != "0")
		.filter(line => line(6) != "0")

	val result_pick = result_filter
		.map(line => (line(5),line(6),line(1) ) )

	val result_drop = result_filter
		.map(line => (line(9),line(10),line(2) ) )
	

    val result3 = result_pick.union(result_drop)

	val real_num = result3.count();
    
 	val result4 = result3.map( line => (
		( 
			(line._1.toDouble/degree).toInt ,
        	(line._2.toDouble/degree).toInt,
       		( 
				find_days(line._3.split(' ')(0).split('-')(1).toInt) 
				+ line._3.split(' ')(0).split('-')(2).toInt -1 
			) 
			/ time_step
		)
		, 1 ) )

    val result5 = result4.reduceByKey( (x,y) => x + y)
	.coalesce(PART)
	.persist(StorageLevel.MEMORY_ONLY)
    
  
     
	val rdd_size = result5.count();
    
	val mean = (real_num-1).toDouble /rdd_size.toDouble; // 
   
	val pow_sum = sc.accumulator[Long](0);
    result5.foreach(line => {
      pow_sum += line._2 * line._2
    }
          
    )
    
    val pow_sum_mean = pow_sum.value.toDouble / rdd_size.toDouble;
    
    val S =  scala.math.sqrt(abs(pow_sum_mean - mean * mean));
    
	println("S : " + S);
	println("mean : " + mean);
	println("pow_sum : " + pow_sum);
	println("pow_sum_mean : " + pow_sum_mean);
	println("rdd_size : " + rdd_size);
    println("real num :" + real_num );

     
    val broad_map = sc.broadcast(result5.collectAsMap());
    
	val sort_result5= result5.collect.toSeq.sortWith(_._2 > _._2)

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
     
    
    val g_rdd = sc.parallelize(sort_result5.take(heuristic), PART).map { line =>
     
		val res = find_weight(line)
     
		val sum_weight_value = res._1
		val sum_weight =   res._2
		val sum_pow_weight =  res._3
      
		val denominator = S * sqrt((rdd_size * sum_pow_weight - sum_weight * sum_weight)/(rdd_size + 1));
		val molecule = sum_weight_value - mean * sum_weight;
		val g_value = molecule / denominator;
      
    	((line._1._1,line._1._2,line._1._3),g_value);
    }

    println("g_rdd")
	val sort_g = g_rdd.collect.toSeq.sortWith(_._2 > _._2)
	
	
	sort_g.take(50).foreach(println(_))
   	println("ver1.0 _ done!!!!!")
    val time_finish = java.lang.System.currentTimeMillis();
    val time = time_finish - time_start;

	val writer = new PrintWriter(new File(result_path + "/result_"+args(0)+
				"_"+args(1)+"_"+ args(2)+"_sampling_v0.4.txt"))


	sort_g.take(50).foreach{
			line => writer.write((line._1._1) + ", " + (line._1._2) + ", " +
			(line._1._3) + " : " + (line._2))
			writer.write("\n")
	}
    writer.write(time.toString);
    println(time);
	writer.close();

	readChar

  }
}
