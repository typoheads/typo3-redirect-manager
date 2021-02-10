--
-- Logs "404 Not Found" request.
--
CREATE TABLE tx_redirectmanager_not_found_log
(
    crdate               int(11) unsigned DEFAULT 0 NOT NULL,
    deleted              int(1) unsigned DEFAULT 0 NOT NULL,

    hash                 varchar(255) DEFAULT '' NOT NULL,
    url                  mediumtext   DEFAULT '' NOT NULL,
    hit_count            int(11) unsigned DEFAULT 0 NOT NULL,
    is_resolved          int(1) unsigned DEFAULT 0 NOT NULL,
    has_reappeared_count int(11) unsigned DEFAULT 0 NOT NULL,

    PRIMARY KEY (hash)
);
