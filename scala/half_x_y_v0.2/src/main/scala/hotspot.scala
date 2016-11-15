//
//	filename	:	hotspot.scala
//	writer		:	Dong Min Kim
//	purpose		:	hotspot analysis (Getis-Ord)
//

import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._
import java.io._
import java.lang.System._
import scala.io.Source

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

//----------------------------------//
//	main function arguments			//
//									//
//	args(0) : cell size				//
//	args(1) : time step				//
//	args(2) : input file directory	//
//	args(3) : output file directory	//
//	args(4) : hostpot number //
//	args(5) : column file path
//	args(6) : sampling rate

//----------------------------------//

  def main(args: Array[String]): Unit = {

	val degree= args(0).toDouble
	val time_step = args(1).toInt
	val input_path = args(2)
	val result_path = args(3)
	val hotspot_num = args(4)

	val column_path = args(5)
	val sampling_rate = args(6).toDouble

// set spark configuration
//----------------------------------------------------------------------------------------------------//
    
	val conf = new SparkConf().
	setAppName("hotspot")
	.set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(Array(
	classOf[Array[String]],
	classOf[Array[Array[String]]],
	hotspot.getClass,
	classOf[java.lang.Class[_]],
	classOf[Array[Int]],
	classOf[scala.reflect.ClassTag$$anon$1],
	classOf[scala.collection.mutable.WrappedArray$ofRef]
	))
	val sc = new SparkContext(conf);
    
	val input = sc.textFile(input_path)

//----------------------------------------------------------------------------------------------------//


// filter noise records and useless data, and quantize data
//----------------------------------------------------------------------------------------------------//
	

	val key_arr = Source.fromFile(column_path).getLines.toList.map { line =>
		line.split(" ")
	}.toArray


	val data = input.map(line => line.split(",").map(elem => elem.trim))
	val header = data.first();
	val result_filter = data.filter(line => line(0) != header(0)).filter(line => line.length == header.length );
		
	
	val key_num = key_arr.length;
	val result3 = result_filter.flatMap( line => {
			val temp = new Array[(String,String)](key_num);
			for (key <- 0 to key_num -1 ){
				temp(key) = (  line( key_arr(key)(0).toInt ), line( key_arr(key)(1).toInt ) );
			
			}			
			
			temp
}

	)
	
	val result4 = result3.map( line => {
		
		(
			( 
				(line._1.toDouble/degree).toInt ,
        			(line._2.toDouble/degree).toInt
			) , 1.toLong 
		)
		} )
	
	val result5 = result4.reduceByKey( (x,y) => x + y)
	.persist(StorageLevel.MEMORY_ONLY)

//----------------------------------------------------------------------------------------------------//


// calculate several values for G-statistics
//----------------------------------------------------------------------------------------------------//

	var data_num = sc.accumulator[Long](0);
	var pow_sum = sc.accumulator[Long](0);
	var ac_cube_size = sc.accumulator[Long](0);
	
	
	result5.foreach( line => {
		ac_cube_size += 1;
		data_num += line._2
		pow_sum += line._2 * line._2;


	})

	val cube_size = ac_cube_size.value;
	
	val pow_sum_mean = pow_sum.value.toDouble / cube_size.toDouble

	val mean = data_num.value.toDouble /cube_size.toDouble; 
  
	val S =  scala.math.sqrt(pow_sum_mean - mean * mean);

//----------------------------------------------------------------------------------------------------//


// caculate g value
//----------------------------------------------------------------------------------------------------//


	val result5_map = result5.collectAsMap()
     
	val broad_map = sc.broadcast(result5_map);

	val neighbor_value = 15;
    
	val sort_result5= result5_map.toSeq.sortWith(_._2 > _._2)

	var top_amount = (cube_size * sampling_rate).toInt
	if (top_amount < 50000) {
		top_amount = 50000;
	}
	if (top_amount > cube_size){
		top_amount = cube_size.toInt;	
	}

	var parallelize_num = (top_amount * 0.002).toInt;
	if ( parallelize_num < 10 ) parallelize_num = 10;

	val g_rdd = sc.parallelize(sort_result5.take(top_amount) , parallelize_num).map{ x =>
		{
			
			var sum_weight_value = 0.0;
			var sum_weight = 0.0;
			var sum_pow_weight = 0.0 ; 
			var i = 0
			var j = 0
			var k = 0
			for ( i <- -neighbor_value to neighbor_value){
				for ( j <- -neighbor_value to neighbor_value ) {
					
						
							var max = abs(i)
							if ( max < abs(j) ) { max = abs(j) }
							
							val weight = 1.0 / pow(2,max);
							if (broad_map.value.get(x._1._1 + i, x._1._2 + j) != None){
								sum_weight_value += weight * broad_map.value(x._1._1 + i, x._1._2 + j);
							}
							sum_weight += weight;
							sum_pow_weight += weight * weight;
						
					
				}
			}
      
			val denominator = S * sqrt((cube_size * sum_pow_weight - sum_weight * sum_weight)/(cube_size - 1));
			val molecule = sum_weight_value - mean * sum_weight;
			val g_value = molecule / denominator;
      		((x._1._1,x._1._2), g_value)
				
			

		}

	}
//----------------------------------------------------------------------------------------------------//

	
	
// pick hotspot and make output file
//----------------------------------------------------------------------------------------------------//

	val sort_g = g_rdd.collect.toSeq.sortWith(_._2 > _._2)
	
	var x_max = -100000;
	var x_min = 100000;
	var y_max = -100000;
        var y_min = 100000;
	sort_g.foreach(line => {
			if (line._1._1 > x_max) x_max = line._1._1;
			if (line._1._1 < x_min) x_min = line._1._1;

			if (line._1._2 > y_max) y_max = line._1._2;
			if (line._1._2 < y_min) y_min = line._1._2;	
		}
	)

	val writer = new PrintWriter(new File(result_path + "/result_hotspot.csv"))
	writer.write(hotspot_num + "," + degree + "," + time_step + ",");
	writer.write(x_max +","+x_min+","+y_max+","+y_min +"\n");


	var p_value = 1;
	sort_g.take(hotspot_num.toInt).foreach{
			line => { 
			writer.write((line._1._1) + ", " + (line._1._2) + ", 0 , " + (line._2) + ", ")
			writer.write(p_value.toString)
			p_value += 1;
			writer.write("\n")
		}
	}

	writer.close();

  }
}


