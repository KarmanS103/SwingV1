DROP SCHEMA IF EXISTS swing;

CREATE SCHEMA IF NOT EXISTS swing DEFAULT CHARACTER SET utf8 ;
USE swing;

select *
from daily_price;

drop table if exists sector;
create table if not exists sector (
sector_id int primary key auto_increment,
sector_name varchar(25) not null
);

insert into sector (sector_name) values
('financials'),
('utilities'),
('consumer discretionary'),
('consumer staples'),
('energy'),
('healthcare'),
('industrials'),
('technology'),
('telecom'),
('materials'),
('real estate');

drop table if exists company;
CREATE TABLE IF NOT EXISTS company (
  company_id INT primary key auto_increment,
  ticker VARCHAR(9) NOT NULL,  
  name VARCHAR(36) NOT NULL,
  sector_id int,
  constraint foreign key(sector_id) references sector (sector_id)
  );
  
  INSERT INTO company (company_id, ticker, name, sector_id) VALUES
('1', 'aapl', 'Apple Inc.', 8),
('2', 'amzn', 'Amazon.com Inc.', 3),
('3', 'ba', 'Boeing Company', 7),
('4', 'bac', 'Bank of America Corp', 1),
('5', 'brkb', 'Berkshire Hathaway Inc. Class B', 1),
('6', 'c', 'Citigroup Inc.', 1),
('7', 'csco', 'Cisco Systems Inc.', 8),
('8', 'cvx', 'Chevron Corporation', 5),
('9', 'fb', 'Facebook Inc. Class A', 8),
('10', 'goog', 'Alphabet Inc. Class C', 8),
('11', 'googl', 'Alphabet Inc. Class A', 8),
('12', 'hd' , 'Home Depot Inc.', 3),
('13', 'intc', 'Intel Corporation', 8),
('14', 'jnj', 'Johnson & Johnson', 6),
('15', 'jpm', 'JPMorgan Chase & Co.', 1),
('16', 'ma', 'Mastercard Incorporated Class A', 1),
('17', 'msft', 'Microsoft Corporation', 8),
('18', 'pfe', 'Pfizer Inc.', 6),
('19', 'pg', 'Procter & Gamble Company', 4),
('20', 't', 'AT&T Inc.', 9),
('21', 'unh', 'UnitedHealth Group Incorporated', 6),
('22', 'v', 'Visa Inc. Class A', 1),
('23', 'vz', 'Verizon Communications Inc.', 9),
('24', 'wfc', 'Wells Fargo & Company', 1),
('25', 'xom', 'Exxon Mobil Corporation', 5);