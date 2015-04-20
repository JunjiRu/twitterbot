CREATE TABLE `tweet_box` (
  `content` text DEFAULT NULL,
  `author` char(255) NOT NULL,
  `interval` int NOT NULL,
  `id` char(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `number` (
  `number` int(11) NOT NULL DEFAULT '0',
  `prime_factor` text,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into tweet_box(content, author)values
('数字だけをリプ：素因数分解して返す．57は例外．', 'system'),
('memo 文字列：文字列をメモとして登録．3時間ごとにリプってくる．', 'system'),
('memo -h 1〜24の整数 文字列：文字列をメモとして登録．指定した数字の時間おきにリプってくる．', 'system'),
('del memoのID：ID=ツイート末尾に付与される3桁文字列．メモを削除する．', 'system'),
('getmemos 文字列：自分が登録したメモを全てリプでもらう．文字列は重複ツイート回避のため．', 'system'),
('delmemos 文字列：自分が登録したメモを全て削除．文字列は重複ツイート回避のため．', 'system');
