install: ./install

install_s:
	./install

debug:
	php src/ppm/ppm.php --ppm --verbose $(ARGS)

run:
	php src/ppm/ppm.php --ppm $(ARGS)