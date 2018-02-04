ALTER TABLE `catpages_pages` ADD `LastUpdate` DATETIME NULL;
ALTER TABLE `catpages`
  DROP `PageFirstImageWidth`,
  DROP `PageFirstImageHeight`,
  DROP `PageFirstImageHoldSize`,
  DROP `PageFirstImageTrimExcess`,
  DROP `PageFirstImageThumbWidth`,
  DROP `PageFirstImageThumbHeight`,
  DROP `PageFirstImageThumbHoldSize`,
  DROP `PageFirstImageThumbTrimExcess`,
  DROP `PageImagesWidth`,
  DROP `PageImagesHeight`,
  DROP `PageImagesHoldSize`,
  DROP `PageImagesTrimExcess`,
  DROP `PageImagesThumbWidth`,
  DROP `PageImagesThumbHeight`,
  DROP `PageImagesThumbHoldSize`,
  DROP `PageImagesThumbTrimExcess`;
UPDATE catpages_pages SET LastUpdate = DatePublish;
UPDATE modules_installed SET Version="4.0.0.1" WHERE Module="catpages";
UPDATE modules_installed SET Version="4.0.0.1" WHERE Module="cats";