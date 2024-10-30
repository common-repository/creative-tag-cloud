/**
 *JavaScript part for Creative Tag Cloud
 * Last modified: 2020/07/29 11:32:02
 *
 * @since      0.1.0
 * @package    creative_tag_cloud
 * @subpackage creative_tag_cloud/public/js
 * @author     Christoph Amthor @ Chatty Mango
 * @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPLv3
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

/**
 * Main function to draw the wave tag cloud
 */
function chattyMangoBuildWaveTagCloud(
  svgId,
  maxFontSize,
  frequency,
  changeWavelength,
  waves,
  lineHeightFactor,
  tagsPerWave
) {
  var width = jQuery('#' + svgId).width();
  var height = jQuery('#' + svgId).height();
  var startX = Math.round(maxFontSize / 2);
  var startY = Math.round(height / waves / 2) + maxFontSize / 2;
  var amplitude = Math.round(startY * 0.5);

  chattyMangoDrawWaves(
    svgId,
    startX,
    startY,
    width,
    height,
    amplitude,
    frequency,
    changeWavelength,
    waves,
    lineHeightFactor,
    tagsPerWave
  );
}

/**
 *	Draws a set of waves
 */
function chattyMangoDrawWaves(
  svgId,
  startX,
  startY,
  width,
  height,
  amplitude,
  frequency,
  changeWavelength,
  waves,
  lineHeightFactor,
  tagsPerWave
) {
  var i,
    offset,
    maxPoints = 20,
    step;

  var stepX = width / frequency;
  var stepY = Math.round((height / waves / 3) * lineHeightFactor);

  for (var j = 0; j < waves; j++) {
    i = 0;
    // shift the horizontal offset for each wave a bit to the right
    offset = Math.round(startX + lineHeightFactor * j);
    do {
      step = Math.round(2 * stepX * Math.pow(changeWavelength, i));
      if (i === 0) {
        var pathPnts =
          'M' +
          chattyMangoRnd1(offset) +
          ' ' +
          chattyMangoRnd1(startY + j * stepY) +
          ' Q ' +
          chattyMangoRnd1(offset + stepX) +
          ' ' +
          chattyMangoRnd1(startY + j * stepY - amplitude) +
          ' ' +
          chattyMangoRnd1(offset + step) +
          ' ' +
          chattyMangoRnd1(startY + j * stepY) +
          ' ';
        i++;
      } else {
        pathPnts +=
          'T ' +
          chattyMangoRnd1(offset + i * step) +
          ' ' +
          chattyMangoRnd1(startY + j * stepY) +
          ' ';
      }
      i++;
    } while (offset + i * step < width && i < maxPoints);
    var path = document.getElementById(svgId + '_path_' + j);
    path.setAttribute('d', pathPnts);

    // remove words from the end
    if (tagsPerWave === 'auto') {
      var textpath = document.getElementById(svgId + '_text_path_' + j);
      if (
        typeof path.getTotalLength !== 'undefined' &&
        typeof textpath.getComputedTextLength !== 'undefined'
      ) {
        var removed = 0;
        if (path.getTotalLength() < textpath.getComputedTextLength() * 1.1) {
          while (
            path.getTotalLength() < textpath.getComputedTextLength() * 1.1 &&
            removed < 20
          ) {
            // remove a word
            jQuery(
              '#' + svgId + '_text_path_' + j + ' tspan:last-child'
            ).remove();
            removed++;
          }
        }
      }
    }
  }
}

/**
 * Main function to draw the spiral tag cloud
 */
function chattyMangoBuildSpiralTagCloud(
  svgId,
  maxFontSize,
  mediumFontSize,
  direction,
  startAngle,
  cycles,
  lineHeightFactor,
  reduceFactor
) {
  var dir, revs, smallerDimension, separation, reduce, spiralPnts;
  var width = jQuery('#' + svgId).width();
  var height = jQuery('#' + svgId).height();
  var centerX = Math.round(width / 2);
  var centerY = Math.round(height / 2);

  // taken out of loops for better performance
  var maxFontSizeFactored = maxFontSize / 2.3;
  var lineHeightFactorFactored = lineHeightFactor * 20;
  var fontSizeRatio = mediumFontSize / maxFontSize;
  var reduceFactorFactored = reduceFactor; //*3

  if (direction.toLowerCase() === 'ccw') {
    dir = 1;
  } else {
    dir = -1;
  }

  if (cycles === 'auto' || cycles === 'size' || cycles < 1) {
    // try to guess a good number of cycles, simplifying the spiral as circles in equidistant steps
    if (width < height) {
      smallerDimension = width;
    } else {
      smallerDimension = height;
    }
    var r = 20; // inner radius
    revs = Math.round(
      (smallerDimension / 2 - r) / lineHeightFactorFactored + 1
    );
    if (revs < 1) {
      revs = 1;
    }
    if (revs > 10) {
      revs = 10;
    }
  } else {
    revs = cycles;
  }

  // compromise between smoothness and speed
  spiralPnts = revs * (1 + lineHeightFactorFactored * (revs - 1));

  // initial separation between spiral lines
  separation = maxFontSizeFactored * lineHeightFactor;

  // factor per round to reduce the separation - assumed linear reduction from largest to medium size tag until half way
  reduce = Math.pow(
    fontSizeRatio,
    (2 / (spiralPnts * revs)) * reduceFactorFactored
  );

  chattyMangoDrawSpiral(
    svgId,
    dir,
    centerX,
    centerY,
    separation,
    revs,
    startAngle,
    reduce,
    spiralPnts
  );

  // redraw the spiral according to the text path
  if (cycles === 'auto') {
    var textpath = document.getElementById(svgId + '_text_path');
    var path = document.getElementById(svgId + '_path');
    // typeof not available for older IE versions with JScript before version 8
    if (
      typeof path.getTotalLength !== 'undefined' &&
      typeof textpath.getComputedTextLength !== 'undefined'
    ) {
      if (dir === 1) {
        var factor = 1.1;
      } else {
        var factor = 1.3;
      }
      if (textpath.getComputedTextLength() < 1000) {
        factor = factor * 2; // avoid collapsing spirals for small amounts of tags
      }
      var factoredTextPathLength = textpath.getComputedTextLength() * factor;
      
      if (path.getTotalLength() > factoredTextPathLength) {
        var chattyMangoReduce = function (revs) {
          if (path.getTotalLength(revs) > factoredTextPathLength && revs > 4) {
            revs--;

            // compromise between smoothness and speed: 50 points per revolution
            spiralPnts = revs * (1 + lineHeightFactorFactored * (revs - 1));

            // factor per round to reduce the separation - assumed linear reduction from largest to medium size tag until half way
            reduce = Math.pow(
              fontSizeRatio,
              (2 / (spiralPnts * revs)) * reduceFactorFactored
            );

            chattyMangoDrawSpiral(
              svgId,
              dir,
              centerX,
              centerY,
              separation,
              revs,
              startAngle,
              reduce,
              spiralPnts
            );
            
            // setTimeout to prevent freezing browser
            setTimeout(function () {
              chattyMangoReduce(revs);
            }, 1);
          }
        };
        chattyMangoReduce(revs);
      } else if (path.getTotalLength() < factoredTextPathLength) {
        var chattyMangoExtend = function (revs) {
          if (path.getTotalLength() < factoredTextPathLength && revs < 20) {
            revs++;

            // compromise between smoothness and speed: 50 points per revolution
            spiralPnts = revs * (1 + lineHeightFactorFactored * (revs - 1));

            // factor per round to reduce the separation - assumed linear reduction from largest to medium size tag until half way
            reduce = Math.pow(
              fontSizeRatio,
              (2 / (spiralPnts * revs)) * reduceFactorFactored
            );

            chattyMangoDrawSpiral(
              svgId,
              dir,
              centerX,
              centerY,
              separation,
              revs,
              startAngle,
              reduce,
              spiralPnts
            );

            // setTimeout to prevent freezing browser
            setTimeout(function () {
              chattyMangoExtend(revs);
            }, 1);
          }
        };
        chattyMangoExtend(revs);
      }
    }
  }
}

/**
 *	Draws a spiral
 *	Credits http://svgdiscovery.com/E3/svg-spiral-path.htm
 * Modified to continuously change distance and to start from outside.
 */
function chattyMangoDrawSpiral(
  svgId,
  dir,
  centerX,
  centerY,
  separation,
  revs,
  startAngle,
  reduce,
  spiralPnts
) {
  var endPoint = parseInt(100); // the center
  var sep;

  var degSep = (2 * Math.PI * revs) / spiralPnts;

  for (var i = spiralPnts; i > endPoint; i--) {
    if (separation > 1) {
      separation = separation * reduce;
    }
    sep = separation / (2 * Math.PI);
    var nextAngle = dir * degSep * i + parseFloat(startAngle);

    var Ax = sep * nextAngle * Math.cos(nextAngle) + centerX;
    var Ay = sep * nextAngle * Math.sin(nextAngle) + centerY;
    if (i === spiralPnts) {
      var pathPnts =
        'M' + chattyMangoRnd1(Ax) + ' ' + chattyMangoRnd1(Ay) + ' S ';
    } else {
      pathPnts += chattyMangoRnd1(Ax) + ' ' + chattyMangoRnd1(Ay) + ' ';
    }
  }
  if ((spiralPnts - endPoint) / 2 + ''.indexOf('.5') !== -1) {
    pathPnts += chattyMangoRnd1(Ax) + ' ' + chattyMangoRnd1(Ay);
  }
  var path = document.getElementById(svgId + '_path');
  path.setAttribute('d', pathPnts);
}

/**
 *	Returns number with one decimal place
 */
function chattyMangoRnd1(num) {
  var dp1 = Math.round(num * 10) / 10;
  return dp1;
}
