### In HOSPOT Directory. type 'sbt assembly'

### spark properties : --driver-memory MEM --executor-memory MEM ( MEM is max value that you can allocate)

### command spark-submit [spark properties] [submission jar] [path to input] [path to output] [degree] [time step size]

### Example :
./bin/spark-submit --master spark://master:7077 \\
	--driver-memory 20g \\
	--executor-memory 20g \\
	.../HOTSPOT/target/scala-2.10/hotspot.jar \\
	 0.001 \\
  	 7 \\	
	 hdfs://path/to/directory\_with\_csvs \\
  	 hdfs://path/to/output 
  	 hot_spot_num
	 column_path



### In "conf/spark-defaults.conf" file setting this variables as this values.
spark.driver.maxResultSize	0


### My team asked to contest master about how to set weight and the contest master said that everything is in the GIS-CUP official website.
As following sentences "This spatial neighborhood is created for the preceding, current, and following time periods (i.e., each cell has 26 neighbors). For simplicity of computation, the weight of each neighbor cell is presumed to be equal." which are in problem definition section, we can interpret this as "each cell has 26 neighbors and we assume that target cell for calculation is influenced by only this 26 neighbor-cells." We call this weight strategy as 'binary-weight-strategy'.

### 
So we selected 'binary-weight-strategy' in our main code. 
Before we sent mail, however, we tried so many weight strategies and we selected one of those strategies(we call this strategy as 'half-weight-strategy')-
The cube weight decreases by 1/2 each time to be away.
So we also include the code with that 'half-weight-stategy' in 'etc/half_weight' folder. If you need to consider the weight as very import element, then please refer this another code.


