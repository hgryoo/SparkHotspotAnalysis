import scala.io.Source
import util.control.Breaks._
import scala.collection.mutable._
import scala.collection.mutable.ListBuffer
import scala.math._

object test1 {
  def main(args: Array[String]) {
    
    try{
      var degree = args(0).toDouble;
      var max = args(1).toInt;
      var time_step = args(2).toInt;
      var my_map = scala.collection.mutable.Map[(Int , Int , Int), Int]()
      var location_map = scala.collection.mutable.Map[(Int , Int ), Int]()
      var num = 0;
      
      breakable{
      for(line <- Source.fromFile("E:/aaa/workspace/test1/data/yellow_tripdata_2015-01.csv").getLines()){
        num = num + 1;
        if (num == max + 1) break;
        if (num % 1000000 == 0) println("---");
        breakable{
          val inputLine = line.split(',');
          
          
          var inputData_lat = inputLine(6);
          if (inputData_lat == "pickup_latitude"){
            //println("trash_data");
            num -= 1;
            break;
          }
          val doubleData_lat = inputData_lat.toDouble;
          if (doubleData_lat == 0) {
            num -= 1;
            //println("trash_data");
            break;
          }
          val index_lat = (doubleData_lat / degree).toInt;
            
          //println(index_lat);
          var inputData_long = inputLine(5);
          val doubleData_long = inputData_long.toDouble;
          if (doubleData_long == 0){
            num -= 1;
            //println("trash_data");
            break;     
          }
          val index_long = (doubleData_long / degree).toInt;
          
          var inputData_date = inputLine(1).split(' ')(0).split('-')(2).toInt;
          var inputData_time = inputLine(1).split(' ')(1).split(':')(0).toInt;
          
          var inputData_ID = ((inputData_date - 1) * 24 + inputData_time) / time_step;
          
         // println(index_long)
          if (location_map.get(index_long, index_lat ) == None){
            location_map((index_long, index_lat)) = 1;
          }
          else{
            location_map((index_long, index_lat)) += 1;
          }
          
          if (my_map.get(index_long, index_lat , inputData_ID) == None){
            my_map((index_long, index_lat, inputData_ID)) = 1;
          }
          else{
            my_map((index_long, index_lat, inputData_ID)) += 1;
          }
          
          //println(index_lat + " : " + my_map(index_lat));
          

        }

      } 
      
      }
      
      
      var mean = (num-1).toDouble / my_map.size.toDouble;
      //mean = BigDecimal(mean).setScale(5, BigDecimal.RoundingMode.HALF_UP).toDouble;
      
      var pow_sum = 0;
       for((x,y,z) <- my_map.keys){
         pow_sum += my_map(x,y,z) * my_map(x,y,z)

       }
       
      var pow_sum_mean = pow_sum.toDouble / my_map.size.toDouble;
     // pow_sum_mean = BigDecimal(pow_sum_mean).setScale(5, BigDecimal.RoundingMode.HALF_UP).toDouble;
      
      var S = sqrt(abs(pow_sum_mean - mean * mean));
     // S = BigDecimal(S).setScale(4, BigDecimal.RoundingMode.HALF_UP).toDouble;
      
     
      var g_map = scala.collection.mutable.Map[(Int , Int , Int), Double]();
      
      println("mean : " + mean);
      println("pow_sum : " + pow_sum);
      println("pow_sum_mean : " + pow_sum_mean);
      println("pow_sum_mean - mean * mean : " + (pow_sum_mean - mean * mean));
      println("S : "+ S);
      println("my_map_size : "+ my_map.size);
      println("num : " + (num - 1));
      println("location_number : " + location_map.size);
      
      
      var pre = 0;
       for((x,y,z) <- my_map.keys){
         pre += 1;
         if (pre % 1000 == 0) println(pre);
         var sum_weight_value = 0.0;//weight x value
         var sum_weight = 0.0;
         var sum_pow_weight = 0.0 ; 
          for((x1,y1,z1) <- my_map.keys){ 
            var dis = List[Int]();
            if (x-x1 >0)
              dis = x-x1 :: dis;
            else
              dis = -x+x1 :: dis;
            
            if (y-y1 > 0)
              dis = y-y1 :: dis
            else
              dis = -y+y1 :: dis
            
            if (z-z1 > 0 )
              dis = z - z1 :: dis;
            else
              dis = -z + z1 :: dis;
           
            
            var sort_list = dis.sortWith(_>_);
           // println(sort_list);
            var weight = 1/pow(2,sort_list(0));
           
            sum_weight_value += weight * my_map(x1,y1,z1);
            sum_weight += weight;
            sum_pow_weight += weight * weight;
            
         }
          
          
        //  println("sum_pow_weight : " + sum_pow_weight);
       //   println("sum_weight : " + sum_weight );
          
          
         var denominator = S * sqrt((my_map.size * sum_pow_weight - sum_weight*sum_weight)/(my_map.size + 1));
        // denominator = BigDecimal(denominator).setScale(4, BigDecimal.RoundingMode.HALF_UP).toDouble;
       //  println("denominator : " + denominator);
         
         
         var molecule = sum_weight_value - mean * sum_weight;
        // molecule = BigDecimal(molecule).setScale(4, BigDecimal.RoundingMode.HALF_UP).toDouble;
         var g_value = molecule / denominator;
      //   println("molecule : " + molecule);
         
         //println(x + ", " + y + ", " + z + " : " + g_value);
         
         g_map((x,y,z)) = g_value;
       }
       
       var sort_map = ListMap(my_map.toSeq.sortWith(_._2 < _._2):_*)
       var sort_g_map = ListMap(g_map.toSeq.sortWith(_._2 < _._2) : _*)
       var hotspot = 0;
       
       println("hotspot");
       breakable{
         for ((x,y,z) <- sort_map.keys){
         hotspot += 1;
         println(hotspot + ":: " + x + ", " + y + ", " + z + " : " + sort_map(x,y,z));
         if (hotspot == 1) println("dd" + sort_g_map(x,y,z));
         if (hotspot == 50) break;
         }
       }
       
      
       println("g_hotspot");
       hotspot = 0;
       breakable{
       for ((x,y,z) <- sort_g_map.keys){
         hotspot += 1;
         println(hotspot + ":: " + x + ", " + y + ", " + z + " : " + sort_g_map(x,y,z));
         if (hotspot == 50) break;
       }
       }
   
     

    }
    catch {
      case ex: Exception => println(ex)
    }
     
   }
  

  
}