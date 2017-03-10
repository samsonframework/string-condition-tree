/**
 * Find equal pairs of string
 *
 * @param origin
 * @param min
 * @returns {*}
 */
let findEqual = (origin, min) => {

    if (Object.keys(origin).length === 0) {
        throw new Error('Origin is empty');
    }

    let char = null;
    let isEquals = true;
    let isEndWord = false;
    let isDiffWord = false;
    let endWord = null;

    // Iterate all words
    each(origin, (string) => {
        // This word is max length
        if (string.length === min) {
            isEquals = false;
            isEndWord = true;
            endWord = string;
            return false;
        }
        let charString = string.charAt(min);
        char = char === null ? charString : char;
        // This word is different regarding to previous
        if (char !== charString) {
            isEquals = false;
            isDiffWord = true;
            endWord = string;
            return false;
        }
    });

    // Do next iteration
    if (isEquals) {
        return findEqual(origin, min + 1);
        // Get result
    } else {
        return {count: min, isEndWord, isDiffWord, endWord};
    }
};

/**
 * Find grouped words by different word in passed positoin
 *
 * @param origin
 * @param position
 * @returns {Array}
 */
let findDiffOrigin = (origin, position) => {
    let matches = {};

    // Iterate all words
    each(origin, (string, value) => {
        let char = string.charAt(position);
        // Add in existing object or create new
        if (matches[char] !== undefined) {
            matches[char][string] = value;
        } else {
            matches[char] = {[string]: value};
        }
    });

    // Convert from object with keys to array of list
    let result = [];
    for (let char in matches) {
        if (matches.hasOwnProperty(char)) {
            result.push(matches[char]);
        }
    }
    return result;
};

/**
 * Remove key from object
 *
 * @param origin
 * @param key
 * @returns {*}
 */
let removeKey = (origin, key) => {
    let copy = Object.assign({}, origin);
    delete copy[key];

    return copy;
};

/**
 * Iterate object
 *
 * @param list
 * @param fn
 */
let each = (list, fn) => {
    for (let i in list) {
        if (list.hasOwnProperty(i)) {
            if (fn(i, list[i]) === false) {
                break;
            }
        }
    }
};

/**
 * Do iteration and compressing
 *
 * @param origin
 * @param min
 * @returns {{char: string, min: (number|*), max: *, list: Array}}
 */
let iterate = (origin, min) => {

    min = min === undefined ? 0 : min;
    // Find equal pairs of words
    let equalResult = findEqual(origin, min);

    // Generate result
    let result = {
        char: equalResult.endWord.substring(min, equalResult.count),
        min: min,
        max: equalResult.count,
        list: []
    };

    // If equal pair of words has word which length is less then current "min" counter
    if (equalResult.isEndWord === true) {
        // Only this word has value
        result['value'] = origin[equalResult.endWord];
        let originCopy = removeKey(origin, equalResult.endWord);

        // Do next iteration if origin has some word for such pattern
        if (Object.keys(originCopy).length > 0) {
            result['list'].push(iterate(originCopy, equalResult.count));
        }
    }

    // If pair of words has different char at postion
    if (equalResult.isDiffWord === true) {
        // Find all different pairs and do iteration by only different grouped words
        let diffList = findDiffOrigin(origin, result.max);
        diffList.forEach((list) => {
            // Push result of iteration
            result['list'].push(iterate(list, result.max));
        });
    }
    return result;
};


module.exports = iterate;
