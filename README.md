# ReadME

Small and rather basic CMS for promoting Amazon products. Need more said? WIP

End goal is to have a simple PHP CMS that will display Amazon products for the Amazon affiliate program. 

## Currently implemented
- You can login to the backend if you manually create the database. Sometime I'll get around to having a dump of the table structures in the assets folder or somewhere that you can import into MySQL or similar DB, but today is not that day.
- You can create pages, and if you have Apache2 web server with rewrite enabled, the website will work as intended. Pages are created in the pages folder (pretty simple ehh?), just follow the template of one of the pages already there.
- That's about it

## Planned
- Backend that lets admins add amazon products into the database. The application will download(?) the images and shit for you so on the product pages, it just pulls the info from the database. Neat huh (I hope)
- Ability to load items from the database and display them all pretty like in cards and stuff, should be cool
- Delete products, this should be handy
- Edit products, also should be handy
- That's probably it for now, if you have a feature request, add it as an issue under the tag 'feature request'. If it's good enough and simple enough for me to code, I might add it, or I might not.

Disclaimer. I know enough PHP to make this shit work, no more, no less. If you see a bug, kindly report it and maybe even give me a hint as to how to fix it. This is free software, therefore it's coded like free software by someone who has no time for this shit.