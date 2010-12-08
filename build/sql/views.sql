-------------------------------------------------------
---cc_playlisttimes
-------------------------------------------------------

CREATE VIEW cc_playlisttimes AS 
SELECT pl.id, COALESCE(t.length, '00:00:00'::time without time zone) AS length
   FROM cc_playlist pl
   LEFT JOIN ( SELECT cc_playlistcontents.playlist_id AS id, 
   sum(cc_playlistcontents.cliplength::interval)::time without time zone AS length
     FROM cc_playlistcontents
     GROUP BY cc_playlistcontents.playlist_id) t ON pl.id = t.id;
