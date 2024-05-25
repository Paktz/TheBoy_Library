Don't forget to turn AUTO_INCREMENT back to 0 after testing 
1 means it's turned on for testing account.

ALTER TABLE Books AUTO_INCREMENT = 0;
ALTER TABLE users AUTO_INCREMENT = 0;
ALTER TABLE borrowings AUTO_INCREMENT = 0;
