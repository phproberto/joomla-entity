var gulp        = require('gulp');
var extension   = require('./package.json');
var config      = require('./gulp-config.json');
var del         = require('del');
var browserSync = require('browser-sync');

var src = '../src';
var libraryName = 'joomla_entity';

var wwwPath = config.wwwDir + '/libraries/' + libraryName + '/vendor/phproberto/joomla-entity/src';

var browserConfig = config.hasOwnProperty('browserConfig') ? config.browserConfig : defaultBrowserConfig;

// BrowserSync: init
gulp.task('browserSync:init', function(done) {
	browserSync.init(browserConfig, done);
});

// BrowserSync: reload
gulp.task('browserSync:reload', function(done) {
	browserSync.reload();
	done();
});

// Clean
gulp.task('clean', function() {
	return del(wwwPath, {force : true});
});

// Copy: src
gulp.task('copy:src', function() {
	return gulp.src(src + '/**',{ base: src })
		.pipe(gulp.dest(wwwPath));
});

// Copy
gulp.task('copy', gulp.series('clean', 'copy:src'));

// Watch
gulp.task('watch', function() {
	gulp.watch(
		src + '/**', 
		gulp.series('copy', 'browserSync:reload')
	);
});

gulp.task('default', gulp.series('copy', 'browserSync:init', 'watch'));
