
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import org.apache.spark.SparkContext._
import scala.math._
import org.apache.spark.storage.StorageLevel
import org.apache.spark.HashPartitioner
import scala.collection.mutable._
import java.io._
import java.lang.System._
import org.apache.spark.sql.SQLContext
import sqlContext.implicits._


object dataframe {

   def find_days(para : Int) : Int = {
	var x = 0
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
  def main(args: Array[String])  {


    val time_start = java.lang.System.currentTimeMillis();
    val degree= args(0).toDouble
    val time_step = args(1).toInt
  	val input_path = args(2)
	val result_path = args(3)
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
    
    val conf = new SparkConf().
    setAppName("neighbor")
	.set("spark.serializer", "org.apache.spark.serializer.KryoSerializer")
	.set("spark.kryo.registrationRequired","true")
	.registerKryoClasses(Array(
	classOf[Array[String]],
	neighbor.getClass,
	classOf[scala.collection.mutable.WrappedArray$ofRef]
	))

    val sc = new SparkContext(conf);

	val sqlContext = new SQLContext(sc);
    
    val input = sc.textFile(input_path)
   
    val data = input.map(line => line.split(",").map(elem => elem.trim))

    val result_filter = data.filter(line => line(0) != "VendorID").filter(line => line(5) != "0")
		.filter(line => line(6) != "0").filter(line => line(9) != "0").filter(line => line(10) != "0")

	val result_pick = result_filter.map(line => (line(6),line(5),line(1) ) )

	val result_drop = result_filter.map(line => (line(10),line(9),line(2) ) )
	
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
		, 1 ) ).toDF


	val result5 = result4.reduceByKey( (x,y) => x + y)
	.filter(line => line._1._1 >= (-90 / degree) && line._1._1 <= (90/degree))
	.filter(line => line._1._2 >= (-180 / degree) && line._1._2 <= (180/degree))
	.coalesce(PART)
	.persist(StorageLevel.MEMORY_ONLY)







  }
}
