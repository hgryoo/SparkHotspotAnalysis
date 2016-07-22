#SparkHotspotAnalysis

##Quick start

```
./bin/spark-submit [spark properties] --class [submission class]
 [submission jar] [path to input] [path to output] [cell size in
 degrees] [time step size in days]
```

##Expected Output Format
Output will be a CSV in the following foramt
```
cell_x, cell_y, time_step, zscore, pvalue
```

##Building
This project uses SBT for a build system. To build the library run following command.
```
sbt run
```

##ResultTest

```
resultComp.exe "baseFileName" "comparedFileName"
```