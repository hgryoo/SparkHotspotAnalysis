
import org.apache.spark.SparkConf
import org.apache.spark.SparkContext
import java.io.StringReader
import com.opencsv.CSVReader
import scala.math._

import org.apache.spark.storage.StorageLevel

object test2 {
  
  def main(args: Array[String]) = {
    val degree= args(0).toDouble
    val time_step = args(1).toInt
    
   // System.setProperty("hadoop.home.dir", "HADOOP_HOME");
    
    val conf = new SparkConf().
    setAppName("test2")
    .setMaster("local");
    
    val sc = new SparkContext(conf);
    
    val input = sc.textFile("D:\\me\\Data_about_Major\\Grad\\yellow_tripdata_2015-01.csv");
    
    
    val result = input.map{ line =>
      val reader = new CSVReader(new StringReader(line));
      reader.readNext();
      
    }
//    
//    result.persist(StorageLevel.DISK_ONLY)
//    
//    val result2 = result.mapPartitionsWithIndex{
//      (idx, iter) => if (idx ==0 ) iter.drop(1) else iter
//    }
//    
//    val result3 = result2.map( line => ( line(5), line(6), line(1) ) )
//    
//    
//    val result4 = result3.map( line => ( ( (line._1.toDouble/degree).toInt ,
//        (line._2.toDouble/0.001).toInt,
//        ( line._3.split(' ')(0).split('-')(2).toInt) / 7 ), 1) )
//        
//    val result5 = result4.reduceByKey( (x,y) => x + y)
//    
//    result5.persist(StorageLevel.MEMORY_AND_DISK)
//    
//    val mean = (result.count()-1).toDouble /result5.count().toDouble; // 
//      //mean = BigDecimal(mean).setScale(5, BigDecimal.RoundingMode.HALF_UP).toDouble; //
//    
//    
//    val pow_sum = sc.accumulator(0);
//    result5.foreach(line => {
//      pow_sum += line._2 * line._2
//    }
//          
//    )
//    
//    val pow_sum_mean = pow_sum.value.toDouble / result5.count().toDouble;
//    
//    val S =  sqrt(abs(pow_sum_mean - mean * mean));

    //val g_map = 
  }
}