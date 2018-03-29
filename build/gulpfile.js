var gulp = require('gulp');

var extension = require('./package.json');
var config    = require('./gulp-config.json');

var requireDir = require('require-dir');
var zip        = require('gulp-zip');
var fs         = require('fs');
var xml2js     = require('xml2js');
var parser     = new xml2js.Parser();

var jgulp = requireDir('./node_modules/joomla-gulp', {recurse: true});
var dir = requireDir('./joomla-gulp-extensions', {recurse: true});

var rootPath = '../extensions/libraries/joomla_entity';

// Override of the release script
gulp.task('release', function (cb) {
	fs.readFile( '../extensions/libraries/joomla_entity/joomla_entity.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];

			var fileName = extension.name + '-v' + version + '.zip';

			return gulp.src([
					rootPath + '/**/*',
					'!' + rootPath + '/vendor/**/docs/**/*',
					'!' + rootPath + '/vendor/**/docs',
					'!' + rootPath + '/vendor/**/doc/**/*',
					'!' + rootPath + '/vendor/**/doc',
					'!' + rootPath + '/vendor/**/composer.*',
					'!' + rootPath + '/vendor/**/build.php',
					'!' + rootPath + '/vendor/**/phpunit.*',
				],{ base: rootPath })
				.pipe(zip(fileName))
				.pipe(gulp.dest('releases'))
				.on('end', cb);
		});
	});
});
