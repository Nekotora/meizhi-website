var gulp = require('gulp');
var preprocess = require('gulp-preprocess');
var browserSync = require("browser-sync").create();
var pug = require('gulp-pug');
var del = require('del');
var less = require('gulp-less');

gulp.task('html', function() {
  gulp.src('./src/*.pug')
    .pipe(preprocess({ context: { curtime: Date.now() } }))
    .pipe(pug())
    .pipe(gulp.dest('./dist/'))
});

gulp.task('scripts', function() {
  gulp.src(['./src/**/*.js'])
    .pipe(preprocess())
    .pipe(gulp.dest('./dist/'))
});

gulp.task('css', function() {
  gulp.src('./src/**/*.css')
    .pipe(preprocess({context: { NODE_ENV: 'production', DEBUG: true}}))
    .pipe(gulp.dest('./dist/'))
});

gulp.task('less', function () {
  return gulp.src('./src/**/*.less')
    .pipe(less())
    .pipe(gulp.dest('./dist/'));
});

gulp.task('static', function() {
  gulp.src('./static/**/*.*')
    .pipe(gulp.dest('./dist/'))
});

gulp.task('vendor', function() {
  gulp.src('./vendor/**/*.*')
    .pipe(gulp.dest('./dist/'))
});

gulp.task('serve', function(done) {
  browserSync.init({
    server: {
      baseDir: './dist',
      serveStaticOptions: {
        extensions: ['html'] // pretty urls
      }
    },
  });
  done();
})

gulp.task('reload', function (done) {
  setTimeout(function(){
    browserSync.reload();
    done();
  }, 300)
});

gulp.task('dev', ['scripts', 'css', 'less', 'html', 'static','vendor', 'serve'], function(cb) {
  gulp.watch('./src/**/*.js', ['scripts']);
  gulp.watch('./src/**/*.less', ['less']);
  gulp.watch('./src/**/*.css', ['css']);
  gulp.watch('./src/**/*.pug', ['html']);
  gulp.watch('./static/**/*.*', ['static']);
  gulp.watch('./vendor/**/*.*', ['vendor']);

  gulp.watch('./dist/**/*.*', ['reload']);
});

gulp.task('clean', () => del(['dist/*'], { dot: true }));

gulp.task('build', ['scripts', 'css', 'less', 'html', 'static', 'vendor'])