
USE BNGRC;

ALTER TABLE bngrc_dispatch_runs
  ADD COLUMN methode ENUM('fifo','smallest','proportional') NOT NULL DEFAULT 'fifo'
  AFTER note;