# Thrift Example Project

Apache Thrift의 사용법 데모를 위한 프로젝트. 

더 상세한 내용은 [이 블로그 포스트](http://blog.appkr.kr/work-n-play/how-to-use-apache-thrift-in-php-part-1/)에서 확인할 수 있다.

## 1. 프로젝트 복제 및 기본 설정

깃허브로부터 프로젝트를 복제한다.

```sh
$ git clone git@github.com:appkr/thrift-example-project.git
```

`.env` 파일을 만든다.
 
```sh
$ cd thrift-example-project
~/thrift-example-project $ cp .env.example .env
```
 
 이 프로젝트가 의존하는 라이브러리를 설치한다.
 
```sh
~/thrift-example-project $ composer install
```
 
 이 프로젝트는 SQLite 데이터베이스를 사용한다. 데이터베이스 파일을 만들고, 스키마를 마이그레이션하고, 테스트 데이터를 심는다.
 
```sh
~/thrift-example-project $ touch database/database.sqlite
~/thrift-example-project $ php artisan migrate
~/thrift-example-project $ php artisan db:seed
```

## 2. Thrift 작동 확인하기

로컬 웹 서버를 기동한다.

```sh
~/thrift-example-project $ php artisan serve
```

새 콘솔 창을 열고, `phpunit`으로 Thrift 클라이언트를 실행해 본다.

```
~/thrift-example-project $ vendor/bin/phpunit
```

