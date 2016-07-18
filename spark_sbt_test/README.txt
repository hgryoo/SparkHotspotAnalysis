#SparkHotSpot

1. http://www.barik.net/archive/2015/01/19/172716/ - hadoop 2.6.0 설치
2. 환경변수 설정
 - HADOOP_HOME : C:\hadoop
 - PATH : $HADOOP_HOME\bin 추가

3. sbt 로 빌드

3-1 . 
build.sbt
project/assembly.sbt
src/main/scala/소스코드 

의 파일구조를 갖게한다.

3-2. build.sbt 가 있는 폴더에서 cmd 에 sbt assembly 입력
3-3. target의 scala..폴더에 jar 파일

4. spark폴더에서 bin\spark-submit --master local ... "jar파일" 인자1 인자2



