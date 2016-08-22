
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
	var x = 0
	var total_days = 0
	if (para == 1)
	{ (0) }
	else{
		for (x <- 2 to para){
			if (x == 2 | x == 4 | x == 6 | x == 8 | x == 9 | x == 11){
				total_days += 31;
			}else if (x == 3){
				total_days += 28;
			}
			else{
				total_days += 30;
			}
		}
	
	}
	(total_days)
  }

  def main(args: Array[String]): Unit = {


	val time_start = java.lang.System.currentTimeMillis();
	val degree= args(0).toDouble
	val time_step = args(1).toInt
	val input_path = args(2)
	val result_path = args(3)
	
	val PART = 100
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
    
	val conf = new SparkConf().
	setAppName("neighbor")
	.set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(Array(
	classOf[Array[String]],
	neighbor.getClass,
	classOf[java.lang.Class[_]],
	classOf[Array[Int]],
	classOf[scala.reflect.ClassTag$$anon$1],
	classOf[scala.collection.mutable.WrappedArray$ofRef]
	))

	val sc = new SparkContext(conf);
    
	val input = sc.textFile(input_path)
    
	val data = input.map(line => line.split(",").map(elem => elem.trim))

	val result_filter = data.filter(line => line(0) != "VendorID").filter(line => line(5) != "0")
		.filter(line => line(6) != "0").filter(line => line(9) != "0").filter(line => line(10) != "0")
		//.persist(StorageLevel.MEMORY_ONLY)

	
	
	val result3 = result_filter.flatMap( line => {
			Array( (line(6),line(5),line(1) ), (line(10),line(9),line(2) ) )
			
			}
)
	
	val result4 = result3.map( line => {
		(
			( 
				(line._1.toDouble/degree).toInt ,
        			(line._2.toDouble/degree).toInt,
       				( 
				find_days(line._3.split(' ')(0).split('-')(1).toInt) 
				+ line._3.split(' ')(0).split('-')(2).toInt -1 
				) 
				/ time_step
			) , 1 
		)
		} )
	
	var result5 = result4.reduceByKey( (x,y) => x + y)
	.filter(line => line._1._1 >= (-90 / degree) && line._1._1 <= (90/degree))
	.filter(line => line._1._2 >= (-180 / degree) && line._1._2 <= (180/degree))
	//.filter(line => line._1._3 >= 0)
	.persist(StorageLevel.MEMORY_ONLY)

	var i = 0
	var j = 0
	var k = 0
	var data_num = sc.accumulator[Long](0);
	var pow_sum = sc.accumulator[Long](0);
	var ac_cube_size = sc.accumulator[Long](0);
	
	val flat_result5 = result5.flatMap( line=> {
		ac_cube_size += 1;
		data_num += line._2
		pow_sum += line._2 * line._2;
		var seq = Map[(Int,Int,Int),Int]();
		for ( i <- -1 to 1){
				for ( j <- -1 to 1 ) {
					for ( k <- -1 to 1 ) {
						seq += ( (line._1._1 + i, line._1._2 + j, line._1._3 + k) -> line._2)
						
				}
			}
		}
		(seq)
		}
	)
	
	val result = flat_result5.reduceByKey( (x,y) => x + y).persist(StorageLevel.MEMORY_ONLY)
	


	//val result5_map = result5.collectAsMap()
     
	//val broad_map = sc.broadcast(result5_map);
    
	val sort_result5= result.collect.toSeq.sortWith(_._2 > _._2)

	val cube_size = ac_cube_size.value;
	val pow_sum_mean = (pow_sum.value.toDouble)  / cube_size.toDouble 
	val mean = (data_num.value).toDouble /cube_size.toDouble; // 
	val S =  scala.math.sqrt(pow_sum_mean - mean * mean);

	


	
	//val sort_result5 = result5.sortBy(_._2 , false).zipWithIndex()
	sort_result5.take(50).foreach{line =>
		
		val denominator = S * sqrt((cube_size * 27 - 27 * 27)/(cube_size - 1));
		val molecule = line._2 - mean * 27;
		println(line._1	+ " : " + molecule / denominator);
	}

	println("S : " + S);
	println("mean : " + mean);
	println("rdd_size : " + cube_size);
	println("real num :" + data_num.value );
	println("g_rdd")

	val time_finish = java.lang.System.currentTimeMillis();
	val time = time_finish - time_start;

	val writer = new PrintWriter(new File(result_path + "/result_"+args(0)+"_"+args(1)+"_neighbor_one_v0.0.4.txt"))
	//writer.write("cell_x, cell_y, time_step, zscore, pvalue \n");
	var p_value = 1.0;

	sort_result5.take(50).foreach{
			line => { 
			val denominator = S * sqrt((cube_size * 27 - 27 * 27)/(cube_size - 1));
			val molecule = line._2 - mean * 27;
			
			writer.write((line._1._1) + ", " + (line._1._2) + ", " +(line._1._3) + " : " + molecule/denominator)
			//writer.write((p_value / cube_size.toDouble).toString)
			//p_value += 1;
			writer.write("\n")
		}
	}
	writer.write(time.toString);
	writer.write("\n data num : "+data_num.value);
	println(time);
	writer.close();

	readChar
  }
}


