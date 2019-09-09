<?php

return [
    'scoreSumQuery' => 'SUM(posts.score+(posts.platinum*180)+(posts.gold*50)+(posts.silver*10))',
    'scoreAvgQuery' => 'AVG(posts.score+(posts.platinum*180)+(posts.gold*50)+(posts.silver*10))'
];