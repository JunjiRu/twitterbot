twitterbot
==========

bot
replyHeyHeyTime.php <-サンプル
config.php <-git管理外にして秘匿すべき情報をdefine
common.php <-最初に呼び出す．DB接続，RestAPIのインスタンス化を実行など共通処理
/twitter <-クラスを適当につっこんでる

今後しようとしていること
watchReplies.phpへの機能追加
　数値を受け取った場合：int型の限界突破
　それ以外："@アカウント名 memo メモ内容"の形式で受け取った場合にはメモ内容を1時間おきにリプで返す

新ファイル
　chatworkの未読件数がある場合には30分おきに通知をリプる
