import AssemblyKeys._

name := "dataframe"

version := "1.0"
organization := "com.databricks"
scalaVersion := "2.10.4"

libraryDependencies ++= Seq(
"org.apache.spark" % "spark-core_2.10" % "1.6.0" % "provided",
"org.apache.spark" %% "spark-sql" % "1.6.0",
"com.databricks" % "spark-csv_2.11" % "1.4.0"
	)

assemblySettings

jarName in assembly := "dataframe.jar"

assemblyOption in assembly :=
(assemblyOption in assembly).value.copy(includeScala = false)


