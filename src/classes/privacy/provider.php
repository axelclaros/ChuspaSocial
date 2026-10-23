<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_chuspasocial\privacy;

defined('MOODLE_INTERNAL') || die();

use core_tag_tag;

/**
 * Privacy provider for the ChuspaSocial plugin.
 *
 * @package    local_chuspasocial
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Returns the reason why this plugin does not store personal data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Deletes all the data of the plugin that is stored in a context.
     *
     * In a course context it deletes the posts of the course with their comments, reactions,
     * tags, reports and files, and the links between the book listings and the course.
     * In the system context it deletes the posts of the general wall in the same way,
     * and also the follows, the book listings and the reports.
     *
     * @param \context $context The context to delete the data from.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = (int) $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $courseid = 0;
        } else {
            return;
        }

        $params = ['courseid' => $courseid];
        $postselect = 'SELECT id FROM {local_chuspasocial_post} WHERE courseid = :courseid';
        $commentselect = "SELECT id FROM {local_chuspasocial_comment} WHERE postid IN ($postselect)";

        $transaction = $DB->start_delegated_transaction();

        try {
            // Delete the tags of the posts.
            $postids = $DB->get_fieldset_select('local_chuspasocial_post', 'id', 'courseid = :courseid', $params);
            foreach ($postids as $postid) {
                core_tag_tag::remove_all_item_tags('local_chuspasocial', 'local_chuspasocial_post', $postid);
            }

            // Delete the reports about the posts and their comments.
            $DB->delete_records_select(
                'local_chuspasocial_report',
                "itemtype = 'comment' AND itemid IN ($commentselect)",
                $params
            );
            $DB->delete_records_select(
                'local_chuspasocial_report',
                "itemtype = 'post' AND itemid IN ($postselect)",
                $params
            );

            // Delete the reactions, the comments and the posts.
            $DB->delete_records_select('local_chuspasocial_reaction', "postid IN ($postselect)", $params);
            $DB->delete_records_select('local_chuspasocial_comment', "postid IN ($postselect)", $params);
            $DB->delete_records('local_chuspasocial_post', $params);

            // Delete the files of the posts.
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'local_chuspasocial', 'postimage');
            $fs->delete_area_files($context->id, 'local_chuspasocial', 'post');

            if ($courseid > 0) {
                // Delete the links between the book listings and the course.
                $DB->delete_records('local_chuspasocial_lstcourse', $params);
            } else {
                // Delete the data that belongs to the system context.
                $DB->delete_records('local_chuspasocial_follow');
                $DB->delete_records('local_chuspasocial_lstcourse');
                $DB->delete_records('local_chuspasocial_listing');
                $DB->delete_records('local_chuspasocial_report');
            }

            $transaction->allow_commit();
        } catch (\Throwable $e) {
            $transaction->rollback($e);
        }
    }
}
