
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._
import java.io._
import java.lang.System._

object neighbor{
  def find_days(para : Int) : Int = {
	var x = para
	var days = 0
	if (para == 1)
	{ (0) }
	else{
		for (x <- 2 to para){
			if (x == 2 | x == 4 | x == 6 | x == 8 | x == 9 | x == 11){
				days += 31;
			}else if (x == 3){
				days += 28;
			}
			else{
				days += 30;
			}
		}
	
	}
(days)
  }

  def main(args: Array[String]): Unit = {


	val time_start = java.lang.System.currentTimeMillis();
	val degree= args(0).toDouble
	val time_step = args(1).toInt
	//val heuristic = args(2).toInt
	val neighbor_value = args(2).toInt
	val input_path = args(3)
	val result_path = args(4)
	val PART = 100
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
    
    val conf = new SparkConf().
    setAppName("neighbor")
	.set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(
	Array(
	classOf[Array[String]],
	neighbor.getClass,
	classOf[scala.collection.mutable.WrappedArray$ofRef]
	)
	)
    
    val sc = new SparkContext(conf);
    
    val input = sc.textFile(input_path)
    
    val data = input.map(line => line.split(",").map(elem => elem.trim))

	val result_filter = data.filter(line => line(0) != "VendorID").filter(line => line(5) != "0").filter(line => line(9) != "0").filter(line => line(10) != "0").filter(line => line(6) != "0")
		//.persist(StorageLevel.MEMORY_ONLY)

	val result_pick = result_filter
		.map(line => (line(6),line(5),line(1) ) )

	val result_drop = result_filter
		.map(line => (line(10),line(9),line(2) ) )
	
	//result_filter.unpersist();

    val result3 = result_pick.union(result_drop)
	var real_num = sc.accumulator[Long](0);
    val result4 = result3.map( line => {
		real_num += 1;
		(
		( 
			(line._1.toDouble/degree).toInt ,
        	(line._2.toDouble/degree).toInt,
       		( 
				find_days(line._3.split(' ')(0).split('-')(1).toInt) 
				+ line._3.split(' ')(0).split('-')(2).toInt -1 
			) 
			/ time_step
		)
		, 1 ) } )

	val result5 = result4.reduceByKey( (x,y) => x + y)
	.filter(line => line._1._1 >= (-90 / degree) && line._1._1 <= (90/degree))
	.filter(line => line._1._2 >= (-180 / degree) && line._1._2 <= (180/degree))

	.persist(StorageLevel.MEMORY_ONLY)
 
	val rdd_size = result5.count();
    
	
	val mean = (real_num.value-1).toDouble /rdd_size.toDouble; // 
   
	
	println("mean : " + mean);

	println("rdd_size : " + rdd_size);
    println("real num :" + real_num.value);

     
    val broad_map = sc.broadcast(result5.collectAsMap());
    
	

    val find_weight = (line : ((Int,Int,Int),Int) ) => {

		var sum_weight_value = 0.0; //weight x value
		var sum_weight = 0.0;// 
		var sum_pow_weight = 0.0 ; // 
		var i = 0
		var j = 0
		var k = 0
		for ( i <- -neighbor_value to neighbor_value){
			for ( j <- -neighbor_value to neighbor_value ) {
				for ( k <- -neighbor_value to neighbor_value ) {
					if (broad_map.value.get(line._1._1 + i, line._1._2 + j, line._1._3 + k) != None){ 
						var max = abs(i)
						if ( max < abs(j) ) { max = abs(j) }
						if ( max < abs(k) ) { max = abs(k) }
						val weight = 1.0 / pow(2,max);
						sum_weight_value += 
							weight * broad_map.value(line._1._1 + i, line._1._2 + j, line._1._3 + k);
            			sum_weight += weight;
            			sum_pow_weight += weight * weight;
					}
				}
			}
		}
		(sum_weight_value,sum_weight,sum_pow_weight)
      
     
    }

	val g_rdd = result5.map{ x =>
     	{
			
			val res = find_weight(x)
			val sum_weight_value = res._1
			val sum_weight =   res._2
			val sum_pow_weight =  res._3
      
			val denominator = 1000 * sqrt((rdd_size * sum_pow_weight - sum_weight * sum_weight)/(rdd_size - 1));
			val molecule = sum_weight_value - mean * sum_weight;
			val g_value = molecule / denominator;
      		((x._1._1,x._1._2,x._1._3), g_value)
				
			

		}

	}



    println("g_rdd")
	val sort_g = g_rdd.collect.toSeq.sortWith(_._2 > _._2)
	
	
	sort_g.take(50).foreach(println(_))
   	

    val time_finish = java.lang.System.currentTimeMillis();
    val time = time_finish - time_start;

	val writer = new PrintWriter(new File(result_path + "/result_"+args(0)+
				"_"+args(1)+"_"+ args(2)+"_origin_neighbor_v0.3.txt"))


	sort_g.take(50).foreach{
			line => writer.write((line._1._1) + ", " + (line._1._2) + ", " +
			(line._1._3) + " : " + (line._2))
			writer.write("\n")
	}
    writer.write(time.toString);
    println(time);
	writer.close();

  }
}


