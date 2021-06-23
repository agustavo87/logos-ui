select * from creators inner join attributes on creators.id = attributes.attributable_id where attributable_type = "creator";

# Comentario

select * from sources inner join attributes on sources.id = attributes.attributable_id where attributable_type = "source";