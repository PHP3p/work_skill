导出数据库中每个表的前十条-（场景-想利用pre环境的数据库做调试）
[mysql] mysqldump导出指定数据库表和条件的数据
1、mysqldump 导数据经常使用，指定数据库，指定表，指定条件，可以这么执行
2、mysqldump -uroot -p --where="1=1 limit 12" -A > D:/dump.sql

3、mysqldump -uroot -p --databases xls --where="1=1 limit 12" > D:/dump12.sql

一、备份test11中的areainfo表：
　　mysqldump --databases test11 --tables areainfo --single-transaction > areainfo.sql
二、备份test11中的tb1和tb2表：
　　mysqldump --databases test11 --tables tb1 tb2 --single-transaction > areainfo.sql

比如clue数据库 ， clue_outcall_tasks 表，id=1
mysqldump -h192.168.11.10 -uroot -pcentos --databases clue --tables clue_outcall_tasks -t --where='id=1' >/tmp/clue_outcall_tasks.sql
参数解释：
--databases 指定数据库
--tables 指定表
--where='' 是筛选条件
-t只导数据，不导结构
-d只导结构，不导数据
