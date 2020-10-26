<?php

/**
 * Emails library.
 */

class Email
{
    /**
     * @var array Domains that are whitelisted as valid email domains (most popular).
     */
    protected $_whitelistDomains = array();

    /**
     * @var array Blacklisted emails.
     */
    protected $_blacklist = array();

    /**
     * @var array Email names containing these words will be filtered.
     */
    protected $_exclude = array();

    /**
     * Builds a new email object.
     *
     * @param $options array Options for the object:
     *                       'whitelistDomains' array Whitelisted domains.
     *                       'blacklist' array Emails blacklist.
     *                       'exclude' array Words exclusion list.
     * @return void.
     */
    public function __construct(array $options = null)
    {
        if (isset($options['whitelistDomains'])) {
            $this->setWhitelistDomains($options['whitelistDomains']);
        }
        if (isset($options['blacklist'])) {
            $this->setBlacklist($options['blacklist']);
        }
        if (isset($options['exclude'])) {
            $this->setExclude($options['exclude']);
        }
    }

    /**
     * Sends an email.
     *
     * @param $args array Data for the sending:
     *                    'fromName': (string) Sender's name.
     *                    'fromEmail': (string) Sender's email.
     *                    'toName': (string) Receiver's name.
     *                    'toEmail': (string) Receiver's email.
     *                    'subject': (string) Mail subject.
     *                    'message': (string) Mail body.
     *                    'isHTML': (boolean) Whether the content is HTML or not.
     * @return boolean TRUE on success; FALSE otherwise.
     */
    public function send(array $args)
    {
        // Defaults
        $fromName = (isset($args['fromName']) ? $args['fromName'] : null);
        $fromEmail = (isset($args['fromEmail']) ? $args['fromEmail'] : null);
        $toName = (isset($args['toName']) ? $args['toName'] : null);
        $toEmail = ($this->validateEmail($args['toEmail'], false) ? $args['toEmail'] : null);
        $to = ($toName && $toEmail ? "{$toName} <{$toEmail}>" : ($toEmail ? $toEmail : null));
        $subject = trim($args['subject']);
        $message = trim($args['message']);
        $contentType = (!isset($args['isHTML']) || (isset($args['isHTML']) && $args['isHTML']) ?
            'text/html' : 'text/plain');
        $headers = ''
            . "MIME-Version: 1.0 \r\n"
            . "Content-type: {$contentType}; charset=iso-8859-1 \r\n"
            . ($fromName && $fromEmail ? "From: {$fromName} <{$fromEmail}> \r\n" : '');

        if (!$to || !$subject || !$message) {
            return false;
        }
        $res = mail($to, $subject, $message, $headers);

        return $res;
    }

    /**
     * Validates email directions.
     *
     * @param $email string Email to validate.
     * @param $strict boolean Domain validity check.
     * @return boolean TRUE if valid; FALSE otherwise.
     */
    public function validateEmail($email, $strict = true)
    {
        $res = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($res && $strict) {
            list($name, $domain) = explode('@', $email);

            // Check against blacklist, exclusion list and domain validation
            $res = !$this->isBlacklisted($email)
                && !$this->shouldBeExcluded($email)
                && $this->isValidDomain($domain);
        }

        return $res;
    }

    /**
     * Tells whether the domain has the required MX record.
     *
     * @param $domain string Domain to validate.
     * @return boolean TRUE if the domain is valid; FALSE otherwise.
     */
    public function isValidDomain($domain)
    {
        $res = (in_array($domain, $this->_whitelistDomains) || checkdnsrr($domain, 'MX'));

        return $res;
    }

    /**
     * Tells whether the email is in the blacklist.
     *
     * @param $email string Email to validate.
     * @return boolean TRUE if it's in the blacklist; FALSE otherwise.
     */
    public function isBlacklisted($email)
    {
        $res = in_array($email, $this->_blacklist);

        return $res;
    }

    /**
     * Tells whether the email has excluded words.
     *
     * @param $email string Email to validate.
     * @return boolean TRUE if it contains exclusion words; FALSE otherwise.
     */
    public function shouldBeExcluded($email)
    {
        $res = false;
        foreach ($this->_exclude as $v) {
            if ($res = (stripos($email, $v) !== false)) {
                break;
            }
        }

        return $res;
    }

    ////
    // Setters and Getters from now on.
    ////

    /**
     * Sets the whitelist for domains.
     *
     * @param $domains array Domains list.
     * @return this.
     */
    public function setWhitelistDomains(array $domains)
    {
        $this->_whitelistDomains = $domains;

        return $this;
    }

    /**
     * Sets the blacklist of emails.
     *
     * @param $blacklist array Blacklist of emails.
     * @return this.
     */
    public function setBlacklist(array $emails)
    {
        $this->_blacklist = $emails;

        return $this;
    }

    /**
     * Sets the words exclusion list.
     *
     * @param $exclude array Exclusion list.
     * @return this.
     */
    public function setExclude(array $words)
    {
        $this->_exclude = $words;

        return $this;
    }
}
