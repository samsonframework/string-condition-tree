/**
 * mocha tests --recursive --watch
 */

let chai = require('chai');
let expect = chai.expect;
let treeCompressor = require('./../index.js');

describe('Tree', function() {
    it('Simple test1', function() {
        expect(treeCompressor({
            '/test/string': '#1',
            '/test/string/inner': '#2',
            '/tube/string': '#3',
            '/second-test/string/inner': '#4',
            '/second-test/inner': '#5',
            '/': '#6',
        })).to.be.eql({
              "char": "/",
              "list": [{
                  "char": "t",
                  "list": [{
                      "char": "est/string",
                      "list": [{
                          "char": "/inner",
                          "list": [],
                          "max": 18,
                          "min": 12,
                          "value": "#2"
                      }],
                      "max": 12,
                      "min": 2,
                      "value": "#1"
                  }, {
                      "char": "ube/string",
                      "list": [],
                      "max": 12,
                      "min": 2,
                      "value": "#3"
                  }],
                  "max": 2,
                  "min": 1
              }, {
                  "char": "second-test/",
                  "list": [{
                      "char": "string/inner",
                      "list": [],
                      "max": 25,
                      "min": 13,
                      "value": "#4"
                  }, {
                      "char": "inner",
                      "list": [],
                      "max": 18,
                      "min": 13,
                      "value": "#5"
                  }],
                  "max": 13,
                  "min": 1
              }, {
                  "char": "",
                  "list": [],
                  "max": 1,
                  "min": 1,
                  "value": "#6"
              }
            ],
            "max": 1,
            "min": 0
        });
    });
});