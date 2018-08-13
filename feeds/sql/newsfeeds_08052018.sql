/**
 * newsfeed_08052018.sql 
 *
 * @package NewsFeed
 * @author Zack <zacharyforreal@gmail.com>
 * @author Jen Villacis <jennifer.villacis@seattlecentral.ed>u
 * @author Eden Dubiso <eden.dubiso@seattlecentral.edu>
 * @version 0.1 2018/08/05
 * @link http://www.edendu.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 */
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS sm18_News_Feeds;
DROP TABLE IF EXISTS sm18_News_Feeds_Categories;


CREATE TABLE sm18_News_Feeds_Categories (
  CategoryID int unsigned NOT NULL AUTO_INCREMENT,
  Category varchar(255) DEFAULT '',
  Description TEXT DEFAULT '',
  DateAdded DATETIME,
  TimesViewed INT DEFAULT 0,
  LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (CategoryID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE sm18_News_Feeds (
  FeedID int unsigned NOT NULL AUTO_INCREMENT,
  CategoryID int unsigned DEFAULT 0,
  SubCategory varchar(255) DEFAULT '',
  Description TEXT DEFAULT '',
  FeedXML varchar(255),
  DateAdded DATETIME,
  TimesViewed INT DEFAULT 0,
  LastUpdated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (FeedID),
  INDEX CategoryID_index(CategoryID),
  FOREIGN KEY (CategoryID) REFERENCES sm18_News_Feeds_Categories(CategoryID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO sm18_News_Feeds_Categories
VALUES (1,'Music', 'Classical, Country, Jazz and Blues', NOW(), NOW(),NOW());

INSERT INTO sm18_News_Feeds_Categories
VALUES (2,'Health', 'Dentistry, Dermatology, Cardiology', NOW(), NOW(),NOW());

INSERT INTO sm18_News_Feeds_Categories
VALUES (3,'Movies', 'Horror, Romance, Superhero', NOW(), NOW(),NOW());



INSERT INTO sm18_News_Feeds
VALUES ('NULL',1,'Classical', 'Classical, Country, Jazz and Blues', 'http://www.bbc.co.uk/music/genres/classical/reviews.rss', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds
VALUES ('NULL',1,'Country', 'Classical, Country, Jazz and Blues', 'http://www.bbc.co.uk/music/genres/country/reviews.rss', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds
VALUES ('NULL',1,'Jazz and Blues', 'Classical, Country, Jazz and Blues', 'http://www.bbc.co.uk/music/genres/jazzandblues/reviews.rss', NOW(), NOW(),NOW());


INSERT INTO sm18_News_Feeds
VALUES ('NULL',2,'Allergy', 'Allergy News', 'https://rss.medicalnewstoday.com/allergy.xml', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds
VALUES ('NULL',2,'Asthma', 'Asthma News', 'https://rss.medicalnewstoday.com/asthma.xml', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds
VALUES ('NULL',2,'Anxiety', 'Anxiety News', 'https://rss.medicalnewstoday.com/anxiety.xml', NOW(),NOW(),NOW());



INSERT INTO sm18_News_Feeds
VALUES ('NULL',3,'Horror', 'Horror, Romance, Superhero', 'https://www.fandango.com/movies/horror/rss/movie-list.rss', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds
VALUES ('NULL',3,'Romance', 'Horror, Romance, Superhero', 'https://www.fandango.com/movies/valentinesday/rss/movie-list.rss', NOW(),NOW(),NOW());

INSERT INTO sm18_News_Feeds 
VALUES ('NULL',3,'Superhero', 'Horror, Romance, Superhero', 'https://www.fandango.com/movies/superhero/rss/movie-list.rss', NOW(),NOW(),NOW());

SET FOREIGN_KEY_CHECKS=1;
