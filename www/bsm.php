<?php

// Steps of the project (in "status" file as a simple string)
$asteps=array(
	      "TOSCAN" => _("project created, need scanning"),
	      "SCANNING" => _("scanning in progress"),
	      "TOCHECK" => _("scanning finished, need picture checking"),
	      "CHECKING" => _("picture checking in progress"),
	      "TOCROP" => _("checking finished, need cropping "),
	      "CROPPING" => _("cropping process started"),
	      "PDFOK" => _("Image PDF generated"),
	      );

define("TYPE_SINGLE",0);
define("TYPE_MULTIPLE",1);
define("TYPE_DATE",2);
define("TYPE_EAN13",3);

// allowed standard metadata and their english names
$ameta=array(
	     "title" => array(_("Title"),_("Title of the book"),TYPE_SINGLE),
	     "author" => array(_("Authors"),_("Authors of the book"),TYPE_MULTIPLE),
	     "publisher" => array(_("Publisher"), _("Publisher of the book"), TYPE_SINGLE),
	     "date" => array(_("Date"),_("Date of publication"),TYPE_DATE),
	     "ean13" => array(_("ISBN"), _("ISBN or EAN13 of the book (its barcode)"), TYPE_EAN13),
	     );

