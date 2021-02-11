--
-- Logs "404 Not Found" request.
--
CREATE TABLE tx_redirectmanager_not_found_log
(
    hash                 varchar(255) DEFAULT '' NOT NULL UNIQUE,
    url                  mediumtext   DEFAULT '' NOT NULL,
    hit_count            int(11) unsigned DEFAULT 0 NOT NULL,
    is_resolved          int(1) unsigned DEFAULT 0 NOT NULL,
    has_reappeared_count int(11) unsigned DEFAULT 0 NOT NULL
);
