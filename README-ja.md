iCalForce
=========

**注意:このドキュメントは、[README.md](https://github.com/qnq777/iCalForce/blob/master/README.md)の翻訳版です。  
このドキュメントは、最新ではない可能性があります。**

Salesforce/Force.com用の、iCalendar (.ics) エクスポート・アプリです。  

Google カレンダーや Outlook.com から、カレンダーのURLを購読することで、  
Salesforceの「行動」を見ることができます。  
Microsoft Outlookのカレンダーに取り込むこともできます。

**サポートするクライアント:**
  * Google calendar
  * Outlook.com
  * Microsoft Outlook

**サポートする Salesforce/Force.com のエディション: EE, UE, DE**

----
### 目次
  * [導入方法](#setup)
  * [Security Enhancement](#security-enhancement)
    * [White-List](#white-list)
    * [Salesforce sharing configurations](#salesforce-sharing-configurations)

----
# <a name="setup"> 導入方法

  1. iCalForce を https://github.com/qnq777/iCalForce.git からダウンロードする。
     ```bash
     $ git clone https://github.com/qnq777/iCalForce.git iCalForce.repo
     ```
     または、[Zipで](https://github.com/qnq777/iCalForce/archive/master.zip) ダウンロードする。

  1. セットアップのスクリプトを実行する。
     ```bash
     $ cd iCalForce.repo/iCalForce
     $ bash ./setup.sh
     ```
  1. サーバー変数を設定する。
    * **USERNAME** - Salesforce アカウントのログイン名。
    * **PASSWORD** - Salesforce アカウントのパスワードとセキュリティートークン。  
      if password is XXXX and security token is YYYY, you should set PASSWORD XXXXYYYY.
    * **OWNERID** - Userの15文字のID。 url パラメーターが省略されたときに使用される。
    * **BASEURL** - 組織の url。 デフォルト値は  
      https://ap1.salesforce.com

    **httpd.conf (apache)**
    ```apache
    SetEnv USERNAME alice@example.com
    SetEnv PASSWORD passSecuritytoken
    SetEnv OWNERID  002i1234567Zz7P
    ```
    **nginx.conf (nginx)**
    ```nginx
    server {
        ...
        location ~ \.php(/|$) {
            ...
            fastcgi_param USERNAME alice@example.com;
            fastcgi_param PASSWORD passSecuritytoken;
            fastcgi_param OWNERID  002i1234567Zz7P;
        }
        ...
    }
    ```

  1. webサイトのルートを **iCalForce.repo/iCalForce/public_html** にする。

    **httpd.conf (apache)**
    ```apache
    ...
    DocumentRoot "/path/to/iCalForce.repo/iCalForce/public_html"
    <Directory "/path/to/iCalForce.repo/iCalForce/public_html">
        ...
    </Directory>
    ...
    ```
    **nginx.conf (nginx)**
    ```nginx
    server {
        ...
        root /path/to/iCalForce.repo/iCalForce/public_html;
        ...
    }
    ```

  1. webサーバーを再起動する。

  1. urlを開く。

    ```
    https://theapp.your.domain.com?u=15charsCaseSensitiveUserId
    ```

# <a name="security-enhancement"> セキュリティー強化

安全に利用するために, 追加のセキュリティー設定をすることを推奨します。

## <a name="#white-list"> ホワイト・リスト

### ホワイト・リストの自動生成
  1. **ユーザ**標準オブジェクトに、カスタムチェックボックス項目 **"UseICalForce__c"** を追加する。
  
  1. **UseICalForce__c** = true をアプリ利用を許可するユーザーに対して行う。

  1. **iCalForce.repo/iCalForce/icalforce/run-create-whitelist.sh**を編集。  
     USERNAME, PASSWORD を書き換える。
     ```bash
     #!/bin/bash
     env \
       USERNAME='alice@example.com' \
       PASSWORD='passSecuritytoken' \
       php create-whitelist.php > whitelist.php
     ```

  1. コマンドを実行する
     ```bash
     $ cd iCalForce.repo/iCalForce/icalforce
     $ bash ./run-create-whitelist.sh
     ```

## <a name="salesforce-sharing-configurations"> Salesforce 共有設定
このアプリのために、**専用ユーザー**の作成と共有設定、アクセス制限設定の実施を推奨します。  

  * 専用ユーザーの作成。
  * 専用ユーザーのための、専用ユーザープロファイルの作成。
  * APIアクセスの許可。
  * ユーザープロファイルによって、アクセスできるオブジェクトを制限する。
    * ユーザ、行動について、**"すべてのデータの閲覧"** を許可。
    * その他の閲覧を禁止。
    * すべての編集・削除を禁止。
  * **項目レベルセキュリティ**でアクセスできる項目を制限。
