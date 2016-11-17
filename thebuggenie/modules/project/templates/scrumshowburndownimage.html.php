<?php include_component('pchart/lineGraph', array('width' => 790, 'height' => 340, 'include_plotter' => true, 'labels' => $labels, 'values_title' => __('Estimated hours', array(), true), 'labels_title' => __('Date'), 'datasets' => $datasets, 'title' => __('Sprint burndown graph - %sprint_name% (starts %starting_date%, ends %scheduled_date%)', array('%sprint_name%' => $milestone->getName(), '%starting_date%' => tbg_formatTime($milestone->getStartingDate(), 23), '%scheduled_date%' => tbg_formatTime($milestone->getScheduledDate(), 23)), true)));