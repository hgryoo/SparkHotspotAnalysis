import scala.io.Source
import util.control.Breaks._
import scala.collection.mutable._
import scala.collection.mutable.ListBuffer
import scala.math._

object test1 {
  def main(args: Array[String]) {
    
    try{
      var degree = Console.readDouble;
      var max = Console.readInt;
      var time_step = Console.readInt();
      var my_map = scala.collection.mutable.Map[(Int , Int , Int), Int]()
      var location_map = scala.collection.mutable.Map[(Int , Int ), Int]()
      var num = 0;
      
      //파일에서 map으로 저장
      breakable{
      for(line <- Source.fromFile("../data/yellow_tripdata_2015-01.csv").getLines()){
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
      
      
      var mean = (num-1).toDouble / my_map.size.toDouble; // 평균
      //mean = BigDecimal(mean).setScale(5, BigDecimal.RoundingMode.HALF_UP).toDouble; //5자리에서 반올림
      
      var pow_sum = 0;
       for((x,y,z) <- my_map.keys){
         pow_sum += my_map(x,y,z) * my_map(x,y,z)

       }
       
      var pow_sum_mean = pow_sum.toDouble / my_map.size.toDouble; // 제곱의 평균
     // pow_sum_mean = BigDecimal(pow_sum_mean).setScale(5, BigDecimal.RoundingMode.HALF_UP).toDouble;
      
      var S = sqrt(abs(pow_sum_mean - mean * mean));
     // S = BigDecimal(S).setScale(4, BigDecimal.RoundingMode.HALF_UP).toDouble;
      
      //gi들 저장하는 map
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
       for((x,y,z) <- my_map.keys){ //Gi 계산
         pre += 1;
         if (pre % 1000 == 0) println(pre);
         var sum_weight_value = 0.0;//weight x value
         var sum_weight = 0.0;//weight들 합
         var sum_pow_weight = 0.0 ; // weight 제곱 들의 합
          for((x1,y1,z1) <- my_map.keys){ //다른 큐브들의 weight 계산
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
            var weight = 1/pow(2,sort_list(0));//거리 젤 먼 걸로 weight 구함.
           
            sum_weight_value += weight * my_map(x1,y1,z1);
            sum_weight += weight;
            sum_pow_weight += weight * weight;
            
         }
          
          
        //  println("sum_pow_weight : " + sum_pow_weight);
       //   println("sum_weight : " + sum_weight );
          
          //분모
         var denominator = S * sqrt((my_map.size * sum_pow_weight - sum_weight*sum_weight)/(my_map.size + 1));
        // denominator = BigDecimal(denominator).setScale(4, BigDecimal.RoundingMode.HALF_UP).toDouble;
       //  println("denominator : " + denominator);
         
         //분자
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
   
     
     //println(my_map.size)
     // var sort_map = ListMap(my_map.toSeq.sortWith(_._2 < _._2):_*)
//      var hotspot = 0;
//      breakable{
//      for((x,y,z) <- my_map.keys){
//        if (my_map(x,y,z) == 1) {
//          //println(z + ", " + y + " , " + z );
//          hotspot += 1;
//        
//        }
//        
//        //hotspot += 1;
//        
//        val dou_x = (x / (1/degree));
//        val dou_y = y / (1/degree);
//       // println(dou_x + ", " + dou_y + " , " + z + " : " + my_map(x,y,z));
//      }
//      
//      println(hotspot);
      
    }
    catch {
      case ex: Exception => println(ex)
    }
     
   }
  

  
}