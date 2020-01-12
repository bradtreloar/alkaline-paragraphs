const gulp = require("gulp");
const autoprefixer = require("gulp-autoprefixer");
const sass = require("gulp-sass");

// Compile .scss files to .css
gulp.task("default", () =>
  gulp
    .src("*.scss")
    .pipe(
      sass().on('error', sass.logError)
    )
    .pipe(
      autoprefixer({
        cascade: false
      })
    )
    .pipe(gulp.dest("."))
);

// Compile .scss files to .css when .scss files are updated.
gulp.task("watch:css", () => {
  gulp.watch("./sass/**/*.scss", gulp.parallel("build:css"));
});
