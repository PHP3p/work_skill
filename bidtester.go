// /bin/zsh -c "cd /Applications/EasySrv/shaoyu/zx_dsp_api/tests && go run bidtester.go 2>&1 | grep -A5 \"替换\""
package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"github.com/google/uuid"
	"io"
	"math/rand"
	"net/http"
	"strings"
	"sync"
	"time"
)

// =====================
// 配置
// =====================
type Tester struct {
	BidURL   string
	SlotID   int
	Interval time.Duration
	Client   *http.Client
}

func New(url string, slotID int, interval time.Duration) *Tester {
	return &Tester{
		BidURL:   url,
		SlotID:   slotID,
		Interval: interval,
		Client: &http.Client{
			Timeout: 5 * time.Second,
		},
	}
}

// =====================
// Android设备
// =====================
type Device struct {
	ID           string
	IP           string
	IMEI         string
	OAID         string
	AndroidID    string
	Brand        string
	Model        string
	UA           string
	Pkg          string
	ScreenWidth  int
	ScreenHeight int
}

func randIP() string {
	return fmt.Sprintf(
		"%d.%d.%d.%d",
		rand.Intn(220)+1,
		rand.Intn(255),
		rand.Intn(255),
		rand.Intn(255),
	)
}
func randIMEI() string {
	imei := ""
	for i := 0; i < 15; i++ {
		imei += fmt.Sprintf("%d", rand.Intn(10))
	}
	return imei
}
func randHex(n int) string {
	str := "abcdef0123456789"
	var b strings.Builder
	for i := 0; i < n; i++ {
		b.WriteByte(
			str[rand.Intn(len(str))],
		)
	}
	return b.String()
}
func NewDevice() Device {
	models := []string{
		"PHQ110",
		"PERM00",
		"23013RK75C",
		"V2247A",
	}
	brands := []string{
		"OPPO",
		"vivo",
		"Xiaomi",
	}
	model := models[rand.Intn(len(models))]
	brand := brands[rand.Intn(len(brands))]
	return Device{
		ID:        uuid.NewString(),
		IP:        randIP(),
		IMEI:      randIMEI(),
		OAID:      randHex(32),
		AndroidID: randHex(16),
		Brand:     brand,
		Model:     model,
		UA: fmt.Sprintf(
			"Mozilla/5.0 (Linux; Android 33; %s Build/TQ3A.230805.001; wv)",
			model,
		),
		Pkg:          "com.wwwscn.yu",
		ScreenWidth:  1080,
		ScreenHeight: 2400,
	}
}

// =====================
// Bid响应
// =====================
type BidResponse struct {
	ID      string `json:"id"`
	Code    int    `json:"code"`
	Message string `json:"message"`
	Cur     string `json:"cur"`
	Ads     []struct {
		AdType              int      `json:"ad_type"`
		InteractionType     int      `json:"interaction_type"`
		Price               float64  `json:"price"`
		Title               string   `json:"title"`
		Description         string   `json:"description"`
		Images              []string `json:"images"`
		Width               int      `json:"width"`
		Height              int      `json:"height"`
		ClickURL            string   `json:"click_url"`
		LandingURL          string   `json:"landing_url"`
		DeeplinkURL         string   `json:"deeplink_url"`
		ImpressionURLs      []string `json:"impression_urls"`
		ClickURLs           []string `json:"click_urls"`
		DeeplinkSuccessURLs []string `json:"deeplink_success_urls"`
		DeeplinkFailURLs    []string `json:"deeplink_fail_urls"`
	} `json:"ads"`
}

// =====================
// 请求Bid
// =====================
func (t *Tester) Bid(d Device) (*BidResponse, error) {
	data := map[string]interface{}{
		"id":                 d.ID,
		"slot_id":            t.SlotID,
		"slot_width":         720,
		"slot_height":        1280,
		"bid_floor":          rand.Intn(30) + 1,
		"bid_floor_cur":      "",
		"app_name":           "悦通行",
		"app_package":        d.Pkg,
		"app_ver":            "3.2.1.0",
		"app_ver_code":       0,
		"app_store_url":      "",
		"ip":                 d.IP,
		"ipv6":               "",
		"user_agent":         d.UA,
		"os_type":            1,
		"os_version":         "33",
		"os_level":           "0",
		"imei":               d.IMEI,
		"oaid":               d.OAID,
		"android_id":         d.AndroidID,
		"imsi":               "460022031928050",
		"idfa":               "",
		"idfv":               "",
		"openudid":           "",
		"caid":               "",
		"caid_version":       "",
		"paid":               "d1dac3c2fdeac776e5a556915500a8f7-4e272e5137ed4671b525f90a52516972-71fe6f612eb70dfbc25e0984a88795cb",
		"mac":                "22:14:14:90:f8:48",
		"imei_md5":           "",
		"android_id_md5":     "57bcd65b9955b8d7ae2ec38a51577290",
		"mac_md5":            "b575a9bd1da1b72a1600d23ae247c35a",
		"vendor":             d.Brand,
		"brand":              d.Brand,
		"model":              d.Model,
		"serialno":           "",
		"screen_orientation": 1,
		"screen_width":       d.ScreenWidth,
		"screen_height":      d.ScreenHeight,
		"screen_dpi":         394,
		"screen_ppi":         320,
		"screen_density":     2.4625,
		"connection_type":    1,
		"device_type":        1,
		"carrier":            1,
		"mccmnc":             "46000",
		"ssid":               "",
		"wifi_mac":           "",
		"rom_version":        "",
		"sys_compiling_time": "1768133677217",
		"app_store_version":  "86021",
		"longitude":          112.96118,
		"latitude":           28.20141,
		"hms":                "",
		"hag":                "86021",
		"hardware_machine":   d.Model,
		"hardware_model":     d.Model,
		"device_name":        d.Model,
		"device_name_md5":    "779e3708d6d9108526a4ea463c7f31fc",
		"init_time":          "1768564007.793487679",
		"startup_time":       "1775688235",
		"upgrade_time":       "1768133677",
		"timezone":           "GMT+08:00",
		"country":            "CN",
		"language":           "zh",
		"memory":             8074538516,
		"hard_disk":          116823110451,
		"cpu_num":            8,
		"cpu_freq":           0,
		"idfa_policy":        0,
		"battery_status":     1,
		"battery_power":      0,
		"boot_mark":          "1d4631c6-d3b8-0886-fad8-0bcdccb46173",
		"update_mark":        "1779346825.6872000000",
		"packages": []string{
			"com.taobao.idlefish",
			"com.sina.weibo",
			"com.jingdong.app.mall",
			"cmb.pb",
			"tv.danmaku.bili",
			"com.suning.mobile.ebuy",
			"com.bankcomm.maidanba",
			"com.netease.cloudmusics",
			"com.mfw.roadbook",
			"com.smile.gifmaker",
			"com.qiyi.video",
			"com.yygame.and.ysjhzz",
			"com.ctrip.android.view",
			"com.chinaConstructionBank",
			"com.sankuai.meituan",
			"com.moutai.mobile",
			"com.sup.android.superb",
			"com.chaoxing.mobile",
			"com.netease.mkeyl",
			"com.autonavi.minimap",
			"com.ifeng.news",
			"com.cmbc.mbank",
			"com.baidu.netdisk",
			"com.taobao.taobao",
			"me.ele",
			"com.zhihu.android",
			"com.eg.android.AlipayGphone",
			"com.zhio",
			"com.tencent.mm",
			"com.xunmeng.pinduoduo",
			"com.ss.android.ugc.aweme",
			"com.ccb.hftx",
		},
	}
	body, _ := json.MarshalIndent(data, "", "  ")
	fmt.Printf("\n========== 请求参数 ==========\n")
	fmt.Printf("%s\n", string(body))
	fmt.Printf("==============================\n\n")
	req, _ := http.NewRequest(
		"POST",
		t.BidURL,
		bytes.NewReader(body),
	)
	req.Header.Set(
		"Content-Type",
		"application/json",
	)
	resp, err := t.Client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()
	if resp.StatusCode == 204 {
		fmt.Printf("\n========== 真实返回 ==========\n")
		fmt.Printf("HTTP状态码: 204 No Content (无广告填充)\n")
		fmt.Printf("==============================\n\n")
		return &BidResponse{Code: 204}, nil
	}
	result, _ := io.ReadAll(resp.Body)
	fmt.Printf("\n========== 真实返回 ==========\n")
	fmt.Printf("HTTP状态码: %d\n", resp.StatusCode)
	fmt.Printf("响应内容: %s\n", string(result))
	fmt.Printf("==============================\n\n")
	var ret BidResponse
	err = json.Unmarshal(
		result,
		&ret,
	)
	return &ret, err
}

// =====================
// URL参数替换
// =====================
func ReplaceURL(
	url string,
	d Device,
	price float64,
) string {
	now := time.Now()
	// 如果价格为0，使用随机价格（0.01-0.50）
	if price <= 0 {
		price = float64(rand.Intn(50)+1) / 100.0
	}
	params := map[string]string{
		"__TS_MS__":          fmt.Sprint(now.UnixMilli()),
		"__TS__":             fmt.Sprint(now.UnixMilli()),
		"__PRICE__":          fmt.Sprintf("%.2f", price),
		"__DOWN_X__":         "500",
		"__DOWN_Y__":         "800",
		"__AB_DOWN_X__":      "500",
		"__AB_DOWN_Y__":      "800",
		"__WIDTH__":          "720",
		"__HEIGHT__":         "1280",
		"__DP_WIDTH__":       fmt.Sprint(d.ScreenWidth),
		"__DP_HEIGHT__":      fmt.Sprint(d.ScreenHeight),
		"__DP_DOWN_X__":      "500",
		"__DP_DOWN_Y__":      "800",
		"_event_time_start_": fmt.Sprint(now.UnixMilli()),
		"_event_time_end_":   fmt.Sprint(now.UnixMilli() + 500),
		"&uuid=":             "&uuid=" + d.IMEI,
		"&ip=":               "&ip=" + d.IP,
		"&model=":            "&model=" + d.Model,
		"&brand=":            "&brand=" + d.Brand,
		"&pkg=":              "&pkg=" + d.Pkg,
	}
	for k, v := range params {
		url = strings.ReplaceAll(
			url,
			k,
			v,
		)
	}
	return url
}

// =====================
// 访问曝光/点击
// =====================
func (t *Tester) Visit(url string, label string) {
	//return
	resp, err := t.Client.Get(url)
	if err != nil {
		fmt.Printf("  ❌ %s 回调失败: %v\n", label, err)
		return
	}
	fmt.Printf("url请求: %s\n", url)
	defer resp.Body.Close()
	fmt.Printf("  ✅ %s 回调成功: HTTP %d\n", label, resp.StatusCode)
}

// =====================
// 完整流程
// =====================
func (t *Tester) Run(num int, concurrency int) {
	sem := make(chan struct{}, concurrency)
	var wg sync.WaitGroup
	for i := 0; i < num; i++ {
		wg.Add(1)
		go func(idx int) {
			defer wg.Done()
			sem <- struct{}{}
			defer func() { <-sem }()
			time.Sleep(time.Duration(idx) * t.Interval)
			device := NewDevice()
			fmt.Printf("\n========== 测试 %d ==========\n", idx+1)
			fmt.Printf("设备ID: %s\n", device.ID)
			fmt.Printf("IP: %s\n", device.IP)
			fmt.Printf("IMEI: %s\n", device.IMEI)
			fmt.Printf("AndroidID: %s\n", device.AndroidID)
			fmt.Println(strings.Repeat("-", 50))
			result, err := t.Bid(device)
			if err != nil {
				fmt.Printf("[测试 %d] 请求失败: %v\n", idx+1, err)
				return
			}
			if result.Code == 204 {
				fmt.Printf("[测试 %d] 无广告填充(204)\n", idx+1)
				return
			}
			if result.Code != 0 {
				fmt.Printf("[测试 %d] 响应码: %d, 消息: %s\n", idx+1, result.Code, result.Message)
			}
			if len(result.Ads) == 0 {
				fmt.Printf("[测试 %d] 无广告填充\n", idx+1)
				return
			}
			for j, ad := range result.Ads {
				fmt.Printf("\n[广告 %d] 详情:\n", j+1)
				fmt.Printf("  - 类型: %d, 交互: %d, 价格: %.2f %s\n", ad.AdType, ad.InteractionType, ad.Price, result.Cur)
				fmt.Printf("  - 标题: %s\n", ad.Title)
				fmt.Printf("  - 尺寸: %dx%d\n", ad.Width, ad.Height)
				fmt.Printf("  - 图片: %d 张\n", len(ad.Images))
				fmt.Printf("  - 曝光URL: %d 个\n", len(ad.ImpressionURLs))
				fmt.Printf("  - 点击URL: %d 个\n", len(ad.ClickURLs))
				fmt.Printf("  - Deeplink成功: %d 个\n", len(ad.DeeplinkSuccessURLs))
				fmt.Printf("  - Deeplink失败: %d 个\n", len(ad.DeeplinkFailURLs))
				fmt.Println("\n[生命周期回调流程]")
				for k, u := range ad.ImpressionURLs {
					time.Sleep(t.Interval)
					replacedURL := ReplaceURL(
						u,
						device,
						ad.Price,
					)
					fmt.Printf("  [替换前] %s\n", u)
					fmt.Printf("  [替换后] %s\n", replacedURL)
					go t.Visit(
						replacedURL,
						fmt.Sprintf("曝光[%d]", k+1),
					)
				}
				time.Sleep(t.Interval)
				for k, u := range ad.ClickURLs {
					time.Sleep(t.Interval)
					go t.Visit(
						ReplaceURL(
							u,
							device,
							ad.Price,
						),
						fmt.Sprintf("点击[%d]", k+1),
					)
				}
				time.Sleep(t.Interval)
				for k, u := range ad.DeeplinkSuccessURLs {
					time.Sleep(t.Interval)
					go t.Visit(
						ReplaceURL(
							u,
							device,
							ad.Price,
						),
						fmt.Sprintf("Deeplink成功[%d]", k+1),
					)
				}
				for k, u := range ad.DeeplinkFailURLs {
					time.Sleep(t.Interval)
					go t.Visit(
						ReplaceURL(
							u,
							device,
							ad.Price,
						),
						fmt.Sprintf("Deeplink失败[%d]", k+1),
					)
				}
			}
			fmt.Printf("\n[测试 %d] 完成\n", idx+1)
			fmt.Println(strings.Repeat("=", 50))
		}(i)
	}
	wg.Wait()
	fmt.Println("\n🎉 所有测试完成!")
}
func main() {
	rand.Seed(time.Now().UnixNano())
	tester := New(
		"http://dspapi.karang.cn/api/bid/v4/zx",
		//"http://127.0.0.1:8098/api/bid/v4/zx",
		//"http://39.105.15.168:8098/api/bid/v4/zx",
		20240518001,
		100*time.Millisecond,
	)
	fmt.Println("开始测试...")
	fmt.Println("目标URL:", tester.BidURL)
	fmt.Println("测试数量: 500")
	fmt.Println("并发数: 2")
	fmt.Println("请求间隔: 100ms")
	fmt.Println(strings.Repeat("-", 50))
	tester.Run(500, 2)
}
