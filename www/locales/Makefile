all: messages.pot fr_FR/LC_MESSAGES/messages.mo

messages.pot: ../*.php
	[ -r $@ ] || touch $@
	xgettext --copyright-holder="Bookscanner.fr Team" --package-name="BookscannerProjectManager" --package-version="1.0" --msgid-bugs-address="team@bookscanner.fr"  --force-po -o $@ --keyword=__ --keyword=_  -L PHP -F --from-code UTF-8 $^

%/LC_MESSAGES/messages.po: messages.pot
	msgmerge -v -U $@ $^

fr_FR/LC_MESSAGES/messages.mo: fr_FR/LC_MESSAGES/messages.po
	msgfmt fr_FR/LC_MESSAGES/messages.po -o fr_FR/LC_MESSAGES/messages.mo

