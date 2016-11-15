import AssemblyKeys._

name := "test2"

version := "1.0"

organization := "com.databricks"

scalaVersion := "2.10.5"

libraryDependencies ++= Seq(
"org.apache.spark" % "spark-core_2.10" % "1.3.0" % "provided",
"com.opencsv" % "opencsv" % "3.8")
	

assemblySettings

jarName in assembly := "test2.jar"

assemblyOption in assembly :=
(assemblyOption in assembly).value.copy(includeScala = false)

