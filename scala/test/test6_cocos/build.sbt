import AssemblyKeys._

name := "test6_cocos"

version := "1.0"
organization := "com.databricks"
scalaVersion := "2.10.4"

libraryDependencies ++= Seq(
"org.apache.spark" % "spark-core_2.10" % "1.6.0" % "provided"
	)

assemblySettings

jarName in assembly := "test6_cocos.jar"

assemblyOption in assembly :=
(assemblyOption in assembly).value.copy(includeScala = false)


