-- スボーツランキング
-- データベース名＞sport
CREATE DATABASE sport;

-- スポーツテーブル
CREATE TABLE sports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

-- 投票結果テーブル
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sport_id INT NOT NULL,
  FOREIGN KEY (sport_id) REFERENCES sports(id) ON DELETE CASCADE
);
CREATE TABLE votes_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_ip VARCHAR(255) NOT NULL,
  sport_id INT NOT NULL,
  CONSTRAINT UNIQUE KEY unique_vote (user_ip, sport_id)
);

-- スポーツ選択欄
INSERT INTO sports (name) VALUES ('サッカー'), ('野球'), ('ラグビー'), ('バスケット'), ('テニス'), ('陸上'), ('水泳'), ('卓球'), ('格闘'), ('その他');

---------------------------------------------------------

-- キャリアアップランキング
-- データベース名＞enterprise
CREATE DATABASE enterprise;

-- オプションテーブル
CREATE TABLE options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

-- 投票結果テーブル
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  option_id INT NOT NULL,
  FOREIGN KEY (option_id) REFERENCES options(id) ON DELETE CASCADE
);
CREATE TABLE votes_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_ip VARCHAR(255) NOT NULL,
  option_id INT NOT NULL,
  CONSTRAINT UNIQUE KEY unique_vote (user_ip, option_id)
);

-- オプション選択欄
INSERT INTO options (name) VALUES ('事業内容'), ('技術力'), ('ネームバリュー'), ('職場環境'), ('年収'), ('勤務地'), ('会社の成長'), ('福利厚生'), ('雰囲気'), ('その他');
