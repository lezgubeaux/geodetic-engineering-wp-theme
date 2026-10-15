<?php
/**
 * Row: placeholder — temporary proof that nested row dispatch works inside a band.
 *
 * Framework source: none (development scaffolding)
 * CSS owner: none — reuses the pending block's `.b-pending`
 * Blocks used: pending
 *
 * Removed once the real row layouts are registered by their own steps.
 *
 * @var array{
 *   note?: string
 * } $args
 *
 * @package VerQuin
 */

defined( 'ABSPATH' ) || exit;

verquin_block_pending( $args['note'] ?? '' );
