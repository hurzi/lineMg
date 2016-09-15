<?php
/**
 * @category demo.php
 * @author   gouki <gouki.xiao@gmail.com>
 * @created 11/2/15 17:12
 * @since
 */

include('XiaoiBot.php');
//$bot = new XiaoiBot( [ 'app_key' => 'cKj3PLDTp61r', 'app_secret' => 'ybC6j5tVAHg1tFUDj4IO' ] );
$bot = new XiaoiBot();
/**
 * 有两种方法给appkey和secret赋值
 */
$bot->setAppInfo( 'QCrCl92wojmX', 'HX8klwdrbOJTPYaQukbj' );
/**
 * 文字识别,这一块由于返回值和种类偏多,最好还是看一下线上的说明
 */
$askResult = $bot->ask('你是谁');
echo "<pre>";
print_r( $askResult );
echo "</pre>";
$audio = $bot->synth( "机器 人" );
if ($audio[0] == 200) {//这里一定要判断一下,毕竟是直接写文件
    file_put_contents( "./audio.spx", $audio[1] );
}
/**
 * 将刚才生成的文件重新识别成文字
 */
$data = $bot->recog('./audio.spx');
echo "<pre>";
print_r( $data );
echo "</pre>";
