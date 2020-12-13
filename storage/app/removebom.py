#--- BOM除去ツール
#--- ついでに CR+LF を LF だけに変換する
#--- 参考: https://qiita.com/gatuwo_jp/items/ea23d513c7d0d5daa080
import sys, os, codecs, shutil

#--- コマンドライン引数の取得
args = sys.argv

#--- カレントパスの取得
current_path = os.getcwd()

#--- 作業用一時ファイル名の設定
tempfile = ''.join([current_path,'/tempfile'])

#--- 処理件数カウント用変数
conv_count = 0

print("BOMファイル除去スクリプト")

#--- 引数がない場合は、エラー出力して終了
if len(sys.argv) == 1:
    print("引数がありません。")
    sys.exit(1)
else:
    #--- 第一引数を取得
    filepath = args[1]

#--- 指定引数のファイルパスの存在チェック
if(os.path.exists(filepath)):

    #--- os.walk関数のループ
    for (current, subfolders, files) in os.walk(filepath):

        #--- 取得したファイル一覧(files)でループ
        #--- filesからfileNameに個別に取得
        for fileName in files:

            #--- 処理対象のファイルパス生成
            target_path = '/'.join([current, fileName])

            #--- UTF-8BOMからUTF-8NOBOMへの変換処理

            #--- 処理対象のファイルをUTF-8BOMとして読込モードでオープン
            with codecs.open(target_path, 'r', 'utf_8_sig') as r:

                #--- 一時ファイルをUTF-8NOBOMで書込モードでオープン
                with codecs.open(tempfile, 'w', 'utf-8') as w:

                    #--- 処理対象ファイルから一行ずつ読込処理(lineに代入)
                    for line in r:

                        #--- CR+LF を LF に変換
                        line = line.replace('\r\n', '\n')

                        #--- 一時ファイルにlineの内容を一行出力
                        w.write(line)

            #--- ファイルの置き換え処理
            #--- 一時ファイルを処理対象ファイルに上書きコピーする
            shutil.copyfile(tempfile, target_path)

            #--- 一時ファイルを削除する
            os.remove(tempfile)

            #処理件数カウントアップ
            conv_count += 1

else:
    #--- 指定引数のパスが存在しない場合、エラー出力して終了
    print("指定されたフォルダがありません。")
    sys.exit(1)

#--- 終了メッセージ
print(filepath + "配下のファイルを駆逐しました（BOMなしに変換）")
print('変換したファイル件数:{}'.format(conv_count))
sys.exit(0)