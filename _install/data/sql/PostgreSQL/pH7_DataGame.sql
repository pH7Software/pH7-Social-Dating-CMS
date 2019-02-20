--
--
-- Title:         SQL Data Game Install File
--
-- Author:        Pierre-Henry Soria <hello@ph7cms.com>
-- Copyright:     (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
-- License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
-- Package:       PH7 / Install / Data / Sql / PostgreSQL
--
--

@sCurrentDate := CURRENT_TIMESTAMP;


INSERT INTO ph7_games_categories (categoryId, name) VALUES
(1, 'Action'),
(2, 'Arcade'),
(3, 'Casino'),
(4, 'Drawing'),
(5, 'Fighting'),
(6, 'Other'),
(7, 'Puzzle'),
(8, 'Racing'),
(9, 'Retro'),
(10, 'Shooting'),
(11, 'Sports');


INSERT INTO ph7_games (gameId, name, title, description, keywords, thumb, file, categoryId, addedDate, downloads, votes, score, views) VALUES
-- Action (1)
(4, '', 'Flashblox', 'Tetris clone', '', 'tetris_clone.png', 'tetris_clone.swf', 1, @sCurrentDate, 0, 0, 0, 0),
(5, '', 'Bell Boys', 'Help the bell boy to deliver their orders to the right floor by controling the elevators', '', 'bell_boys.png', 'bell_boys.swf', 1, @sCurrentDate, 0, 0, 0, 0),
(6, '', 'Frogger', 'This is a real classic simple and addictive. Just help the frog to cross the street with heavy traffic', '', 'frogger.png', 'frogger.swf', 1, @sCurrentDate, 0, 0, 0, 0),

-- Arcade (2)
(1, '', 'America Fights Back', 'Think you can do better in the war? Well heres your chance.Use your mouse and click to fire your guns! Watch your energy level to make sure you dont die. There are no second chances or extra', 'America Fights Back', 'america_strikes_back.png', 'america_strikes_back.swf', 2, @sCurrentDate, 0, 0, 0, 0),
(2, '', 'Altex', 'Be fast and shoot them all!', 'Altex', 'altex.png', 'altex.swf', 2, @sCurrentDate, 0, 0, 0, 0),
(3, '', 'Alpine Escape', 'Catch the brides falling from the airship but don''t shoot it down - shoot down the fighter plane in', 'Alpine Escape', 'alpine_escape.png', 'alpine_escape.swf', 2, @sCurrentDate, 0, 0, 0, 0),

-- Casino (3)
(7, '', 'Blackjack', 'This is a nice Blackjack games that allow playing upto 5 hands at once', '', 'blackjack.png', 'blackjack.swf', 3, @sCurrentDate, 0, 0, 0, 0),
(8, '', 'The Blackjack Casino', 'One of the most popular Casino games', '', 'blackjack_casino.png', 'blackjack_casino.swf', 3, @sCurrentDate, 0, 0, 0, 0),
(9, '', 'Casino - Let It Ride', 'Click on the chip amount you wish to bet. Press DEAL button to get cards. Determine if your hand is worth keeping or dropping one of your bets.', '', 'let_ride.png', 'let_ride.swf', 3, @sCurrentDate, 0, 0, 0, 0),

-- Drawing (4)
(10, '', 'Boeing', 'NA', 'Boeing', 'boeing.jpg', 'boeing.swf', 4, @sCurrentDate, 0, 0, 0, 0),
(11, '', 'Book', 'NA', 'Book', 'book.jpg', 'book.swf', 4, @sCurrentDate, 0, 0, 0, 0),
(12, '', 'Burj_al_Arab', 'NA', 'Burj_al_Arab', 'burj_al_arab.jpg', 'burj_al_arab.swf', 4, @sCurrentDate, 0, 0, 0, 0),

-- Fighting (5)
(13, '', 'Asteroid Field+', 'Hit as many asteriods as possible in the asteroid field', '', 'asteroid.png', 'asteroid.swf', 5, @sCurrentDate, 0, 0, 0, 0),
(14, '', 'Brighton Bounty Hunter', 'Shoot all that you can see on screen', '', 'brighton_bounty_hunter.png', 'brighton_bounty_hunter.swf', 5, @sCurrentDate, 0, 0, 0, 0),
(15, '', 'Sheepteroids', 'It is Sheep-teroids that we are shooting!', '', 'sheepteroids.png', 'sheepteroids.swf', 5, @sCurrentDate, 0, 0, 0, 0),

-- Other (6)
(16, '', 'Blackjack Fever', 'Blackjack Las Vegas style - Double Down Split and More - Get your practice in before making your t', 'Blackjack Fever', 'bjfever.png', 'bjfever.swf', 6, @sCurrentDate, 0, 0, 0, 0),
(17, '', 'Beermat', 'A game about fliking beermats.', 'Beermat', 'beermat.png', 'beermat.swf', 6, @sCurrentDate, 0, 0, 0, 0),
(18, '', 'Beeku Big Adventure Ch1', 'Run around as Beeku blast away bugs and critters with your machine gun.', 'Beeku''s Big Adventure Ch1', 'beeku_big_adventure.png', 'beeku_big_adventure.swf', 6, @sCurrentDate, 0, 0, 0, 0),

-- Puzzle (7)
(19, '', 'Addem Up', 'Place the next tile from the queue on the board. If all surrounding tiles add up to that number they', 'Addem Up', 'addem_up.png', 'addem_up.swf', 7, @sCurrentDate, 0, 0, 0, 0),
(20, '', 'Aga Maze', 'A mazing game', 'Aga Maze', 'aga_maze.png', 'aga_maze.swf', 7, @sCurrentDate, 0, 0, 0, 0),
(21, '', 'Air Heads', 'Fly your balloon across building but avoid the smog above.', 'Air Heads', 'air_heads.png', 'air_heads.swf', 7, @sCurrentDate, 0, 0, 0, 0),

-- Racing (8)
(22, '', 'Stai Ruere', 'Protesters have gathered around a barracks area! It''s your duty to control them and maintain peace a', 'Stai Ruere', 'stai_ruere.png', 'stai_ruere.swf', 8, @sCurrentDate, 0, 0, 0, 0),
(23, '', 'Stay The Distance', 'Great horse racing game. Easy but a bit harsh.', 'Stay The Distance', 'stay_the_distance.png', 'stay_the_distance.swf', 8, @sCurrentDate, 0, 0, 0, 0),
(24, '', 'TGFG Racing', 'Race around the track smashing into your opponents to knock them off and picking up turbo so you can', 'TGFG Racing', 'tgfg_racing.png', 'tgfg_racing.swf', 8, @sCurrentDate, 0, 0, 0, 0),

-- Retro (9)
(25, '', '12 Puzzle', 'Fit the puzzle pieces together to solve the puzzle!', '12 Puzzle', '12_puzzle.png', '12_puzzle.swf', 9, @sCurrentDate, 0, 0, 0, 0),
(26, '', '24 Puzzle', 'Align the 24 numbers in order from 1-24 in this 3D puzzle.', '24 Puzzle', '24_puzzle.png', '24_puzzle.swf', 9, @sCurrentDate, 0, 0, 0, 0),
(27, '', '3D Frogger', 'Frogger takes on a new look in this 3 dimensional version of the classic game. Make your way across', '3D Frogger', '3d_frogger.png', '3d_frogger.swf', 9, @sCurrentDate, 0, 0, 0, 0),

-- Shooting (10)
(28, '', 'Amok Madman', 'Get tought by an expert in this fun shooting game.', 'Amok Madman', 'amok.png', 'amok.swf', 10, @sCurrentDate, 0, 0, 0, 0),
(29, '', 'Aderans Forest', 'Aderans Forest is under attack. Shoot all the enemies before they destroy it! Click the mouse to shoot.', 'Aderans Forest', 'aderans_forest.png', 'aderans_forest.swf', 10, @sCurrentDate, 0, 0, 0, 0),
(30, '', 'AO-War On Iraq', 'Shoot down Iraqi Terrorists Jeeps Grenaders and hostile villagers using AK Machine guns.', 'AO-War On Iraq', 'war_on_iraq.png', 'war_on_iraq.swf', 10, @sCurrentDate, 0, 0, 0, 0),

-- Sports (11)
(31, '', 'Bullseye', 'Play this classic darts game.', 'Bullseye', 'bullseye.png', 'bullseye.swf', 11, @sCurrentDate, 0, 0, 0, 0),
(32, '', 'Billiards', 'Billiards Flash Game. So famous, you have to play with our Cue Sports game right now. Finally, our Billiards flash game is simple to use, smart and modern.', 'Cue Sports, Cuesports, Billiard, Billiards, Snooker, poll, Billiards balls, Pool Balls', 'billiards.png', 'billiards.swf', 11, @sCurrentDate, 0, 0, 0, 0),
(33, '', 'Bowling', 'Here you are. A superb bowling game. Turn your speakers up!Throw the ball when you see the sign. In order to throw the ball push the go botton. Keep it pressed until the yellow field at t', 'Bowling', 'bowling.png', 'bowling.swf', 11, @sCurrentDate, 0, 0, 0, 0);
