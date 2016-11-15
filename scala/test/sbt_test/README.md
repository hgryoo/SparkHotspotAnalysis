#SparkHotspotAnalysis

##How To Use Sbt

```
 sbt
 > compile
 > package
 > assembly
```

##Sbt Project Setting
build.sbt file has settings for this project
```
lazy val root = (project in file(".")).
  settings(
    name := "test1",
    version := "1.0",
    scalaVersion := "2.11.8"
  )
```

##assembly plugin setting
if you want to use assembly command, you have to add assembly.sbt file in project
```
addSbtPlugin("com.eed3si9n" % "sbt-assembly" % "0.14.3")
```

##Result
Output jar file will be in the ./target/(scala version)
```
./target/(scala version)/(classname).jar
```

##Execute
This jar file can be executed by following command
```
java -jar (classname)-assembly-1.0.jar (arguments)
```
