#SparkHotSpot

1. http://www.barik.net/archive/2015/01/19/172716/ - hadoop 2.6.0 ��ġ
2. ȯ�溯�� ����
 - HADOOP_HOME : C:\hadoop
 - PATH : $HADOOP_HOME\bin �߰�

3. sbt �� ����

3-1 . 
build.sbt
project/assembly.sbt
src/main/scala/�ҽ��ڵ� 

�� ���ϱ����� �����Ѵ�.

3-2. build.sbt �� �ִ� �������� cmd �� sbt assembly �Է�
3-3. target�� scala..������ jar ����

4. spark�������� bin\spark-submit --master local ... "jar����" ����1 ����2



