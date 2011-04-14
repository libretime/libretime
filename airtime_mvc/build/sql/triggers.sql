----------------------------------------------------------------------------------
--calculate_position()
----------------------------------------------------------------------------------
DROP FUNCTION calculate_position() CASCADE;

CREATE FUNCTION calculate_position() RETURNS trigger AS
    '
    BEGIN
    	IF(TG_OP=''INSERT'') THEN
        	UPDATE cc_playlistcontents SET position = (position + 1) 
		WHERE (playlist_id = new.playlist_id AND position >= new.position AND id != new.id);
        END IF;
        IF(TG_OP=''DELETE'') THEN
        	UPDATE cc_playlistcontents SET position = (position - 1) 
		WHERE (playlist_id = old.playlist_id AND position > old.position);
        END IF;
        RETURN NULL;
    END;
    '
	LANGUAGE 'plpgsql';

CREATE TRIGGER calculate_position AFTER INSERT OR DELETE ON cc_playlistcontents
FOR EACH ROW EXECUTE PROCEDURE calculate_position();

