
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._
import java.io._
import java.lang.System._

object hotspot{
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


	val degree= args(0).toDouble
	val time_step = args(1).toInt
	val input_path = args(2)
	val result_path = args(3)

	val top_percent = 0.10 //pick top rate of all cube
	val conf = new SparkConf().
	setAppName("hotspot")
	.set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(Array(
	classOf[Array[String]],
	hotspot.getClass,
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
	
	val result5 = result4.reduceByKey( (x,y) => x + y)
	.filter(line => line._1._1 >= (-90 / degree) && line._1._1 <= (90/degree)) //not in earth
	.filter(line => line._1._2 >= (-180 / degree) && line._1._2 <= (180/degree)) //not in earth
	.persist(StorageLevel.MEMORY_ONLY)

	var data_num = sc.accumulator[Long](0);
	var pow_sum = sc.accumulator[Long](0);
	var ac_cube_size = sc.accumulator[Long](0);

	result5.foreach( line => {
		ac_cube_size += 1;
		data_num += line._2
		pow_sum += line._2 * line._2;
	})

	val cube_size = ac_cube_size.value; //cube_num
	
	val pow_sum_mean = pow_sum.value.toDouble / cube_size.toDouble // mean of power of value
    
	var top_amount = (cube_size * top_percent).toInt // we will pick top amount
	if (top_amount < 50000) {
		top_amount = 50000;
	}
	val mean = data_num.value.toDouble /cube_size.toDouble; 
  
	val S =  scala.math.sqrt(pow_sum_mean - mean * mean); //calculate S

	val result5_map = result5.collectAsMap() 
     
	val broad_map = sc.broadcast(result5_map); //broadcast all of cube
    
	val sort_result5= result5_map.toSeq.sortWith(_._2 > _._2) //sort by value cube

	val neighbor_value = 1 

	val g_rdd = sc.parallelize(sort_result5.take(top_amount) , (top_amount * 0.002).toInt).map{ x =>
		{
			
			var sum_value = 0.0;
			val sum_neighbor = 27; 
			var i = 0
			var j = 0
			var k = 0
			for ( i <- -neighbor_value to neighbor_value){
				for ( j <- -neighbor_value to neighbor_value ) {
					for ( k <- -neighbor_value to neighbor_value ) {
						
						if (broad_map.value.get(x._1._1 + i, x._1._2 + j, x._1._3 + k) != None){
							sum_value += broad_map.value(x._1._1 + i, x._1._2 + j, x._1._3 + k);
				
						}
					}
				}
			}
      
			val denominator = S * sqrt((cube_size * sum_neighbor - sum_neighbor * sum_neighbor)/(cube_size - 1));
			val molecule = sum_value - mean * sum_neighbor;
			val g_value = molecule / denominator;
      		((x._1._1,x._1._2,x._1._3), g_value)
				
			

		}

	}

	
	val sort_g = g_rdd.collect.toSeq.sortWith(_._2 > _._2) //sort by g_value
	

	val writer = new PrintWriter(new File(result_path + "/result_hotspot.csv")) //make csv file
	writer.write("cell_x, cell_y, time_step, zscore, pvalue \n");
	var p_value = 1.0;

	sort_g.take(50).foreach{
			line => { 
			writer.write((line._1._1) + ", " + (line._1._2) + ", " +(line._1._3) + ", " + (line._2) + ", ")
			writer.write((p_value / cube_size.toDouble).toString)
			p_value += 1;
			writer.write("\n")
		}
	}

	writer.close();
	

  }
}


