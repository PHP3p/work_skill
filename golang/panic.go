package main

import "fmt"
/**
https://www.cnblogs.com/liuzhongchao/p/10112739.html
https://www.cnblogs.com/zndxall/archive/2018/09/04/9586088.html
https://studygolang.com/articles/14722
https://blog.csdn.net/pmlpml/article/details/83069721
*/
/**
错误使用方式：
#defer recover()

正确的使用这样的方式：

defer func() {
  recover()
}()
*/
func main(){

	defer func(){ // 必须要先声明defer，否则不能捕获到panic异常
		fmt.Println("c")
		if err:=recover();err!=nil{
			fmt.Println(err) // 这里的err其实就是panic传入的内容，55
		}
		fmt.Println("d")
	}()

	f()
}

func f(){
	fmt.Println("a")
	panic(55)
	fmt.Println("b")
	fmt.Println("f")
}

/*
输出结果：

a
c
d
exit code 0, process exited normally.*/
