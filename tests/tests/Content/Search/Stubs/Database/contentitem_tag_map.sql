CREATE TABLE `jml_contentitem_tag_map` (
	`type_alias` TEXT NOT NULL DEFAULT '',
	`core_content_id` INTEGER NOT NULL ,
	`content_item_id` INTEGER NOT NULL,
	`tag_id` INTEGER NOT NULL,
	`tag_date` DATETIME NOT NULL DEFAULT,
	`type_id` INTEGER NOT NULL
);
