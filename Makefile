all: lexers parsers

lexers: src/Lexer/Configfile_Lexer.php src/Lexer/Template_Lexer.php
parsers: src/Parser/Configfile_Parser.php src/Parser/Template_Parser.php

src/Lexer/Configfile_Lexer.php: src/Lexer/Configfile_Lexer.plex
	php ./utilities/make-lexer.php src/Lexer/Configfile_Lexer.plex src/Lexer/Configfile_Lexer.php

src/Lexer/Template_Lexer.php: src/Lexer/Template_Lexer.plex
	php ./utilities/make-lexer.php src/Lexer/Template_Lexer.plex src/Lexer/Template_Lexer.php

src/Parser/Configfile_Parser.php: src/Parser/Configfile_Parser.y
	php ./utilities/make-parser.php src/Parser/Configfile_Parser.y src/Parser/Configfile_Parser.php

src/Parser/Template_Parser.php: src/Parser/Template_Parser.y
	php ./utilities/make-parser.php src/Parser/Template_Parser.y src/Parser/Template_Parser.php

clean:
	rm -f src/Lexer/Configfile_Lexer.php src/Lexer/Template_Lexer.php src/Parser/Configfile_Parser.php src/Parser/Template_Parser.php
