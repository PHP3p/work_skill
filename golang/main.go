package main

import (
	"fmt"
	"fyne.io/fyne/v2/app"
	"fyne.io/fyne/v2/container"
	"fyne.io/fyne/v2/dialog"
	"fyne.io/fyne/v2/widget"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"github.com/flopp/go-findfont"
	"github.com/golang/freetype/truetype"
	"net/url"
	"strconv"
	"strings"
	"sync"
)

func init() {
	fontPath, err := findfont.Find("msyh.ttf")
	if err != nil {
		fmt.Println("fontPath：", err)
	}
	fmt.Printf("Found 'arial.ttf' in '%s'\n", fontPath)

	// load the font with the freetype library
	// 原作者使用的ioutil.ReadFile已经弃用
	fontData, err := os.ReadFile(fontPath)
	if err != nil {
		fmt.Println("fontData：", err)
	}
	_, err = truetype.Parse(fontData)
	if err != nil {
		fmt.Println("truetype：", err)
	}
	os.Setenv("FYNE_FONT", fontPath)
}
func main() {
	myApp := app.New()
	myWindow := myApp.NewWindow("批量下载工具")

	// 创建输入框和按钮
	urlInput := widget.NewEntry()
	urlInput.SetPlaceHolder("http://lipinai.com/1.jpg")

	savePathInput := widget.NewEntry()
	savePathInput.SetPlaceHolder("./")

	downloadButton := widget.NewButton("开始下载", func() {
		urls := strings.Split(urlInput.Text, ",")
		savePath := savePathInput.Text
		if savePath=="" {
			savePath="./"
		}
		// 创建保存路径目录
		if err := os.MkdirAll(savePath, os.ModePerm); err != nil {
			dialog.NewError(err, myWindow).Show()
			return
		}
		// 批量下载图片
		down(urls, savePath)
		successDialog := dialog.NewInformation("Success", savePath, myWindow)
		successDialog.Show()

		//fmt.Sprintf("下载图片失败：", err)
		//dialog.NewError(err, myWindow).Show()
		//fileName := filepath.Base(url)
		//filePath := filepath.Join(savePath, fileName)
	})

	// 创建布局并显示窗口
	content := container.NewVBox(
		widget.NewLabel("请输入批量地址（用逗号分隔）："),
		urlInput,
		widget.NewLabel("请设置保存路径："),
		savePathInput,
		downloadButton,
	)
	myWindow.SetContent(content)
	myWindow.ShowAndRun()
}
// call func demo
func han() {
	links := []string{
		"https://p9-pc-sign.douyinpic.com/tos-cn-i-0813c001/oENLjATxYDASxhIyBsfNACgdPDNAZvENeAzMAa~tplv-dy-aweme-images:q75.webp?x-expires=1703646000&x-signature=IU%2FwNfyCxWD65BRX7t2hqVtLSKk%3D&from=3213915784&s=PackSourceEnum_PUBLISH&se=false&sc=image&biz_tag=aweme_images&l=20231127114826FD2181842DD28F02AD98",
		"https://p3-pc-sign.douyinpic.com/tos-cn-i-0813c001/oYjwMfCeDzyBsxAZDgANAOLTENPaNSAhOcIAYA~tplv-dy-aweme-images:q75.webp?x-expires=1703646000&x-signature=v%2BLHVycvOng5OzUYHZ74vo2fmd8%3D&from=3213915784&s=PackSourceEnum_PUBLISH&se=false&sc=image&biz_tag=aweme_images&l=20231127114826FD2181842DD28F02AD98",
		"https://p6-pc-sign.douyinpic.com/tos-cn-i-0813c001/osBezhgxENyATsALICjAPDfANsxtDANYMtAQZa~tplv-dy-aweme-images:q75.webp?x-expires=1703646000&x-signature=OlkrF%2B04xV3Qk3D8sQ64AcTXevs%3D&from=3213915784&s=PackSourceEnum_PUBLISH&se=false&sc=image&biz_tag=aweme_images&l=20231127114826FD2181842DD28F02AD98",
		"https://p3-pc-sign.douyinpic.com/tos-cn-i-0813c001/oYFGiAAIlDApTqsGCMeDg0nVAYA9ftAQIqbEDw~tplv-dy-aweme-images:q75.webp?x-expires=1703646000&x-signature=lc%2Fg5VmozVKYY6knqjyEg%2B24Nfs%3D&from=3213915784&s=PackSourceEnum_PUBLISH&se=false&sc=image&biz_tag=aweme_images&l=20231127114826FD2181842DD28F02AD98",
	}
	down(links, "")
}
func down(links []string, savePath string) {
	var wg sync.WaitGroup
	wg.Add(len(links))
	fmt.Printf("savePath saved to %s successfully!\n", savePath)
	for i, link := range links {
		go func(url string, i int) {
			defer wg.Done()
			resp, err := http.Get(url)
			if err != nil {
				fmt.Printf("Failed to fetch image from %s due to: %s\n", url, err)
				return
			}
			defer resp.Body.Close()

			contentType := resp.Header.Get("Content-Type")
			fmt.Println("Content-Type:", contentType)

			filename := getFilename(url, savePath, i, "") // 根据URL生成文件名，这里可能需要你自定义该函数
			file, err := os.Create(filename)

			/*fileName := filepath.Base(url)
			filePath := filepath.Join(savePath, fileName)*/
			if err != nil {
				fmt.Printf("Failed to create file %s due to: %s\n", filename, err)
				return
			}
			defer file.Close()
			_, err = io.Copy(file, resp.Body)
			if err != nil {
				fmt.Printf("Failed to save image to %s due to: %s\n", filename, err)
				return
			}

			fmt.Printf("Image saved to %s successfully!\n", filename)
		}(link, i)
	}
	wg.Wait()
}

// 根据URL生成文件名
func getFilename(uri string, savePath string, i int, name string) string {
	fmt.Printf("savePath", savePath)

	// 解析链接中的URL
	parsedURL, err := url.Parse(uri)
	if err != nil {
		fmt.Println("无法解析链接:", err)
	}
	// 提取文件扩展
	fileExt := filepath.Ext(parsedURL.Path)
	name = fileExt
	if name == "" {
		name = ".webp"
	}
	fmt.Println("文件扩展:", fileExt)
	return savePath + "/" + strconv.Itoa(i) + name // 默认文件名。
}
