if [ -z "$KALS_BRANCH" ]; then KALS_BRANCH=voc4fun-server/master; fi
if [ -z "$KALS_PATH" ]; then KALS_PATH=/var/www/voc4fun-server; fi
if [ -z "$KALS_DIR" ]; then KALS_DIR=/var/www; fi

#echo $KALS_DIR

#cd $KALS_DIR
git clone git://github.com/pulipulichen/voc4fun-server.git "$KALS_PATH"
git reset --hard origin/master