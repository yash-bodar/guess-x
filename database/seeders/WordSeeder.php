<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * // YB - 15-09-2026 Seed dictionary with curated 5, 6, and 7 letter English words
     */
    public function run(): void
    {
        $words5 = [
            'ABOUT', 'ABOVE', 'ABUSE', 'ACTOR', 'ACUTE', 'ADAPT', 'ADMIT', 'ADOPT', 'ADULT', 'AFTER',
            'AGAIN', 'AGENT', 'AGREE', 'AHEAD', 'ALARM', 'ALBUM', 'ALERT', 'ALIKE', 'ALIVE', 'ALLOW',
            'ALONE', 'ALONG', 'ALTER', 'AMONG', 'ANGER', 'ANGLE', 'ANGRY', 'APART', 'APPLE', 'APPLY',
            'ARENA', 'ARGUE', 'ARISE', 'ARRAY', 'ASIDE', 'ASSET', 'AUDIO', 'AUDIT', 'AVOID', 'AWAKE',
            'AWARD', 'AWARE', 'BADLY', 'BAKER', 'BASES', 'BASIC', 'BASIS', 'BEACH', 'BEGAN', 'BEGIN',
            'BEGUN', 'BEING', 'BELOW', 'BENCH', 'BILLY', 'BIRTH', 'BLACK', 'BLAME', 'BLIND', 'BLOCK',
            'BLOOD', 'BOARD', 'BOOST', 'BOOTH', 'BOUND', 'BRAIN', 'BRAND', 'BREAD', 'BREAK', 'BREED',
            'BRIEF', 'BRING', 'BROAD', 'BROKE', 'BROWN', 'BUILD', 'BUILT', 'BUYER', 'CABLE', 'CALIF',
            'CARRY', 'CATCH', 'CAUSE', 'CHAIN', 'CHAIR', 'CHART', 'CHASE', 'CHEAP', 'CHECK', 'CHEST',
            'CHIEF', 'CHILD', 'CHINA', 'CHOSE', 'CIVIL', 'CLAIM', 'CLASS', 'CLEAN', 'CLEAR', 'CLICK',
            'CLOCK', 'CLOSE', 'COACH', 'COAST', 'COULD', 'COUNT', 'COURT', 'COVER', 'CRAFT', 'CRASH',
            'CRAZY', 'CREAM', 'CRIME', 'CROSS', 'CROWD', 'CROWN', 'CURVE', 'CYCLE', 'DAILY', 'DANCE',
            'DATED', 'DEALT', 'DEATH', 'DEBUT', 'DELAY', 'DEPTH', 'DOING', 'DOUBT', 'DOZEN', 'DRAFT',
            'DRAMA', 'DRAWN', 'DREAM', 'DRESS', 'DRILL', 'DRINK', 'DRIVE', 'DROVE', 'DYING', 'EAGER',
            'EARLY', 'EARTH', 'EIGHT', 'ELITE', 'EMPTY', 'ENEMY', 'ENJOY', 'ENTER', 'ENTRY', 'EQUAL',
            'ERROR', 'EVENT', 'EVERY', 'EXACT', 'EXIST', 'EXTRA', 'FAITH', 'FALSE', 'FAULT', 'FIBER',
            'FIELD', 'FIFTH', 'FIFTY', 'FIGHT', 'FINAL', 'FIRST', 'FIXED', 'FLASH', 'FLEET', 'FLOOR',
            'FLUID', 'FOCUS', 'FORCE', 'FORTH', 'FORTY', 'FORUM', 'FOUND', 'FRAME', 'FRANK', 'FRAUD',
            'FRESH', 'FRONT', 'FRUIT', 'FULLY', 'FUNNY', 'GIANT', 'GIVEN', 'GLASS', 'GLOBE', 'GOING',
            'GRACE', 'GRADE', 'GRAND', 'GRANT', 'GRASS', 'GRAVE', 'GREAT', 'GREEN', 'GROSS', 'GROUP',
            'GROWN', 'GUARD', 'GUESS', 'GUEST', 'GUIDE', 'HAPPY', 'HARRY', 'HEART', 'HEAVY', 'HENCE',
            'HENRY', 'HORSE', 'HOTEL', 'HOUSE', 'HUMAN', 'IDEAL', 'IMAGE', 'INDEX', 'INNER', 'INPUT',
            'ISSUE', 'JAPAN', 'JIMMY', 'JOINT', 'JONES', 'JUDGE', 'KNOWN', 'LABEL', 'LARGE', 'LASER',
            'LATER', 'LAUGH', 'LAYER', 'LEARN', 'LEASE', 'LEAST', 'LEAVE', 'LEGAL', 'LEVEL', 'LEWIS',
            'LIGHT', 'LIMIT', 'LINKS', 'LIVES', 'LOCAL', 'LOGIC', 'LOOSE', 'LOWER', 'LUCKY', 'LUNCH',
            'LYING', 'MAGIC', 'MAJOR', 'MAKER', 'MARCH', 'MARIA', 'MATCH', 'MAYBE', 'MAYOR', 'MEANT',
            'MEDIA', 'METAL', 'MIGHT', 'MINOR', 'MINUS', 'MIXED', 'MODEL', 'MONEY', 'MONTH', 'MORAL',
            'MOTOR', 'MOUNT', 'MOUSE', 'MOUTH', 'MOVIE', 'MUSIC', 'NEEDS', 'NEVER', 'NEWLY', 'NIGHT',
            'NOISE', 'NORTH', 'NOTED', 'NOVEL', 'NURSE', 'OCCUR', 'OCEAN', 'OFFER', 'OFTEN', 'ORDER',
            'OTHER', 'OUGHT', 'PAINT', 'PANEL', 'PAPER', 'PARTY', 'PEACE', 'PETER', 'PHASE', 'PHONE',
            'PHOTO', 'PIECE', 'PILOT', 'PITCH', 'PLACE', 'PLAIN', 'PLANE', 'PLANT', 'PLATE', 'POINT',
            'POUND', 'POWER', 'PRESS', 'PRICE', 'PRIDE', 'PRIME', 'PRINT', 'PRIOR', 'PRIZE', 'PROOF',
            'PROUD', 'PROVE', 'QUEEN', 'QUICK', 'QUIET', 'QUITE', 'RADIO', 'RAISE', 'RANGE', 'RAPID',
            'RATIO', 'REACH', 'READY', 'REFER', 'RIGHT', 'RIVAL', 'RIVER', 'ROBOT', 'ROMAN', 'ROUGH',
            'ROUND', 'ROUTE', 'ROYAL', 'RURAL', 'SCALE', 'SCENE', 'SCOPE', 'SCORE', 'SENSE', 'SERVE',
            'SEVEN', 'SHALL', 'SHAPE', 'SHARE', 'SHARP', 'SHEET', 'SHELF', 'SHELL', 'SHIFT', 'SHIRT',
            'SHOCK', 'SHOOT', 'SHORT', 'SHOWN', 'SIGHT', 'SINCE', 'SIXTH', 'SIXTY', 'SIZED', 'SKILL',
            'SLEEP', 'SLIDE', 'SMALL', 'SMART', 'SMILE', 'SMITH', 'SMOKE', 'SOLID', 'SOLVE', 'SORRY',
            'SOUND', 'SOUTH', 'SPACE', 'SPARE', 'SPEAK', 'SPEED', 'SPEND', 'SPENT', 'SPLIT', 'SPOKE',
            'SPORT', 'STAFF', 'STAGE', 'STAKE', 'STAND', 'START', 'STATE', 'STEAM', 'STEEL', 'STICK',
            'STILL', 'STOCK', 'STONE', 'STOOD', 'STORE', 'STORM', 'STORY', 'STRIP', 'STUCK', 'STUDY',
            'STUFF', 'STYLE', 'SUGAR', 'SUITE', 'SUPER', 'SWEET', 'TABLE', 'TAKEN', 'TASTE', 'TAXES',
            'TEACH', 'TEETH', 'TEXAS', 'THANK', 'THEFT', 'THEIR', 'THEME', 'THERE', 'THESE', 'THICK',
            'THING', 'THINK', 'THIRD', 'THOSE', 'THREE', 'THREW', 'THROW', 'TIGHT', 'TIMES', 'TIRED',
            'TITLE', 'TODAY', 'TOPIC', 'TOTAL', 'TOUCH', 'TOUGH', 'TOWER', 'TRACK', 'TRADE', 'TRAIN',
            'TREAT', 'TREND', 'TRIAL', 'TRIED', 'TRIES', 'TRUCK', 'TRULY', 'TRUST', 'TRUTH', 'TWICE',
            'UNDER', 'UNDUE', 'UNION', 'UNITY', 'UNTIL', 'UPPER', 'UPSET', 'URBAN', 'USAGE', 'USUAL',
            'VALID', 'VALUE', 'VIDEO', 'VIRUS', 'VISIT', 'VITAL', 'VOICE', 'WASTE', 'WATCH', 'WATER',
            'WHEEL', 'WHERE', 'WHICH', 'WHILE', 'WHITE', 'WHOLE', 'WHOSE', 'WOMAN', 'WOMEN', 'WORLD',
            'WORRY', 'WORSE', 'WORST', 'WORTH', 'WOULD', 'WOUND', 'WRITE', 'WRONG', 'WROTE', 'YIELD',
            'YOUNG', 'YOUTH',
        ];

        $words6 = [
            'ABROAD', 'ACCEPT', 'ACCESS', 'ACROSS', 'ACTING', 'ACTION', 'ACTIVE', 'ACTUAL', 'ADVICE', 'ADVISE',
            'AFFECT', 'AFFORD', 'AFRAID', 'AGENCY', 'AGENDA', 'AGREED', 'ALMOST', 'ALWAYS', 'AMOUNT', 'ANIMAL',
            'ANNUAL', 'ANSWER', 'ANYONE', 'ANYWAY', 'APPEAL', 'APPEAR', 'AROUND', 'ARRIVE', 'ARTIST', 'ASPECT',
            'ASSESS', 'ASSIST', 'ASSUME', 'ATTACK', 'ATTEND', 'AUGUST', 'AUTHOR', 'AVENUE', 'BACKED', 'BARELY',
            'BATTLE', 'BEAUTY', 'BECOME', 'BEFORE', 'BEHALF', 'BEHIND', 'BELIEF', 'BELONG', 'BERLIN', 'BETTER',
            'BEYOND', 'BISHOP', 'BORDER', 'BOTTLE', 'BOTTOM', 'BOUGHT', 'BRANCH', 'BREATH', 'BRIDGE', 'BRIGHT',
            'BRING', 'BROKEN', 'BUDGET', 'BURDEN', 'BUREAU', 'BUTTON', 'CAMERA', 'CANCER', 'CANDID', 'CANNOT',
            'CARBON', 'CAREER', 'CASTLE', 'CASUAL', 'CAUGHT', 'CENTER', 'CENTRE', 'CHANCE', 'CHANGE', 'CHARGE',
            'CHOICE', 'CHOOSE', 'CHURCH', 'CIRCLE', 'CLIENT', 'CLOSED', 'CLOSER', 'COFFEE', 'COLUMN', 'COMBAT',
            'COMING', 'COMMON', 'COMPLY', 'COPPER', 'CORNER', 'COSTLY', 'COUNTY', 'COUPLE', 'COURSE', 'COVERS',
            'CREATE', 'CREDIT', 'CRISIS', 'CUSTOM', 'DAMAGE', 'DANGER', 'DEALER', 'DEBATE', 'DECADE', 'DECIDE',
            'DEFEAT', 'DEFEND', 'DEFINE', 'DEGREE', 'DEMAND', 'DEPEND', 'DEPUTY', 'DESERT', 'DESIGN', 'DESIRE',
            'DETAIL', 'DETECT', 'DEVICE', 'DIFFER', 'DINNER', 'DIRECT', 'DOCTOR', 'DOLLAR', 'DOMAIN', 'DOUBLE',
            'DRIVEN', 'DRIVER', 'DURING', 'EASILY', 'EATING', 'EDITOR', 'EFFECT', 'EFFORT', 'EIGHTH', 'EITHER',
            'ELEVEN', 'EMERGE', 'EMPIRE', 'EMPLOY', 'ENABLE', 'ENDING', 'ENERGY', 'ENGAGE', 'ENGINE', 'ENOUGH',
            'ENSURE', 'ENTIRE', 'ENTITY', 'EQUITY', 'ESCAPE', 'ESTATE', 'ETHNIC', 'EXCEED', 'EXCEPT', 'EXCUSE',
            'EXPAND', 'EXPECT', 'EXPERT', 'EXPORT', 'EXTEND', 'EXTENT', 'FABRIC', 'FACTOR', 'FAILED', 'FAIRLY',
            'FAMILY', 'FAMOUS', 'FATHER', 'FELLOW', 'FEMALE', 'FIGURE', 'FILING', 'FINGER', 'FINISH', 'FISCAL',
            'FLIGHT', 'FLYING', 'FOLLOW', 'FORCED', 'FOREST', 'FORGET', 'FORMAL', 'FORMAT', 'FORMER', 'FOSTER',
            'FOUGHT', 'FOURTH', 'FRENCH', 'FRIEND', 'FUTURE', 'GARDEN', 'GATHER', 'GENDER', 'GERMAN', 'GLOBAL',
            'GOLDEN', 'GROUND', 'GROWTH', 'GUILTY', 'HANDED', 'HANDLE', 'HAPPEN', 'HARDLY', 'HEADED', 'HEALTH',
            'HEIGHT', 'HIDDEN', 'HOLDER', 'HONEST', 'IMPACT', 'IMPORT', 'INCOME', 'INDEED', 'INFANT', 'INFORM',
            'INJURY', 'INSIDE', 'INTEND', 'INTENT', 'INVEST', 'ISLAND', 'ITSELF', 'JERSEY', 'JOSEPH', 'JUNIOR',
            'KILLED', 'LABOUR', 'LATEST', 'LATTER', 'LAUNCH', 'LAWYER', 'LEADER', 'LEAGUE', 'LEAVES', 'LEGACY',
            'LENGTH', 'LESSON', 'LETTER', 'LIGHTS', 'LIKELY', 'LINKED', 'LIQUID', 'LISTEN', 'LITTLE', 'LIVING',
            'LOCATE', 'LONELY', 'LOVELY', 'LUXURY', 'MAINLY', 'MAKING', 'MANAGE', 'MANNER', 'MANUAL', 'MARGIN',
            'MARINE', 'MARKED', 'MARKET', 'MARTIN', 'MASTER', 'MATTER', 'MATURE', 'MEDIUM', 'MEMBER', 'MEMORY',
            'MENTAL', 'MERELY', 'MERGER', 'METHOD', 'MIDDLE', 'MILLER', 'MINING', 'MINUTE', 'MIRROR', 'MOBILE',
            'MODERN', 'MODEST', 'MODULE', 'MOMENT', 'MORRIS', 'MOSTLY', 'MOTHER', 'MOTION', 'MOVING', 'MURDER',
            'MUSEUM', 'MUTUAL', 'MYSELF', 'NATION', 'NATIVE', 'NATURE', 'NEARBY', 'NEARLY', 'NEEDLE', 'NELSON',
            'NEPHEW', 'NICKEL', 'NOBODY', 'NORMAL', 'NOTICE', 'NOTION', 'NUMBER', 'OBJECT', 'OBTAIN', 'OFFICE',
            'OFFSET', 'ONLINE', 'OPENED', 'OPPOSE', 'OPTION', 'ORANGE', 'ORIGIN', 'OUTPUT', 'OXFORD', 'PACKED',
            'PALACE', 'PALMER', 'PARENT', 'PARISH', 'PARKER', 'PARTLY', 'PATENT', 'PEOPLE', 'PERIOD', 'PERMIT',
            'PERSON', 'PHRASE', 'PICKED', 'PLANET', 'PLAYER', 'PLEASE', 'PLENTY', 'POCKET', 'POLICE', 'POLICY',
            'POSTAL', 'POWDER', 'PRAYER', 'PREFER', 'PRETTY', 'PRINCE', 'PRISON', 'PROFIT', 'PROPER', 'PROVEN',
            'PUBLIC', 'PURSUE', 'RAISED', 'RANDOM', 'RARELY', 'RATHER', 'RATING', 'READER', 'REALLY', 'REASON',
            'RECALL', 'RECENT', 'RECORD', 'REDUCE', 'REFORM', 'REGARD', 'REGIME', 'REGION', 'RELATE', 'RELIEF',
            'REMAIN', 'REMOTE', 'REMOVE', 'REPAIR', 'REPEAT', 'REPLAY', 'REPORT', 'RESCUE', 'RESIGN', 'RESORT',
            'RESULT', 'RETAIL', 'RETAIN', 'RETURN', 'REVEAL', 'REVIEW', 'REWARD', 'RISING', 'ROBUST', 'ROLLER',
            'RUBBER', 'RULING', 'RUNNER', 'SAFETY', 'SALARY', 'SAMPLE', 'SAVING', 'SCHEME', 'SCHOOL', 'SCREEN',
            'SEARCH', 'SEASON', 'SECOND', 'SECRET', 'SECTOR', 'SECURE', 'SEEING', 'SELDOM', 'SELECT', 'SELLER',
            'SENIOR', 'SERIES', 'SERVER', 'SETTLE', 'SEVERE', 'SEXUAL', 'SHADOW', 'SHARED', 'SHIELD', 'SHOULD',
            'SIGNAL', 'SIGNED', 'SILENT', 'SILVER', 'SIMPLE', 'SIMPLY', 'SINGLE', 'SISTER', 'SLIGHT', 'SMOOTH',
            'SOCIAL', 'SOLELY', 'SOUGHT', 'SOURCE', 'SOVIET', 'SPEECH', 'SPIRIT', 'SPREAD', 'SPRING', 'SQUARE',
            'STABLE', 'STATUS', 'STEADY', 'STOLEN', 'STRAIN', 'STREAM', 'STREET', 'STRESS', 'STRICT', 'STRIKE',
            'STRING', 'STRONG', 'STRUCK', 'STUDIO', 'SUBMIT', 'SUDDEN', 'SUFFER', 'SUMMER', 'SUMMIT', 'SUPPLY',
            'SURELY', 'SURVEY', 'SWITCH', 'SYMBOL', 'SYSTEM', 'TALENT', 'TARGET', 'TARIFF', 'TASTED', 'TEMPLE',
            'TENANT', 'TENDER', 'TENNIS', 'THANKS', 'THEORY', 'THIRTY', 'THOUGH', 'THREAT', 'THROUG', 'TIMING',
            'TISSUE', 'TITLED', 'TOILET', 'TOMATO', 'TONGUE', 'TOWARD', 'TRACKS', 'TRAGIC', 'TRAVEL', 'TREATY',
            'TRENDS', 'TRIBAL', 'TRIPOD', 'TROPHY', 'TRUCKS', 'TWELVE', 'TWENTY', 'UNABLE', 'UNIQUE', 'UNITED',
            'UNLESS', 'UNLIKE', 'UPDATE', 'URGENT', 'USEFUL', 'VALLEY', 'VALUED', 'VARIED', 'VICTIM', 'VICTOR',
            'VIEWER', 'VILLAG', 'VIRGIN', 'VIRTUE', 'VISION', 'VISUAL', 'VOICES', 'VOLUME', 'WALKER', 'WANTED',
            'WARNER', 'WARREN', 'WATERS', 'WEAKLY', 'WEAPON', 'WEEKLY', 'WEIGHT', 'WINDOW', 'WINNER', 'WINTER',
            'WISDOM', 'WONDER', 'WORKER', 'WRIGHT', 'WRITER', 'YELLOW',
        ];

        $words7 = [
            'ABILITY', 'ABSENCE', 'ACADEMY', 'ACCOUNT', 'ACCUSED', 'ACHIEVE', 'ACQUIRE', 'ADDRESS', 'ADVANCE', 'ADVISED',
            'ADVISER', 'AGAINST', 'AIRLINE', 'AIRPORT', 'ALCOHOL', 'ALLEGED', 'ALREADY', 'ANALYST', 'ANCIENT', 'ANOTHER',
            'ANXIETY', 'ANYBODY', 'APPLIED', 'ARRANGE', 'ARRIVAL', 'ARTICLE', 'ASSAULT', 'ASSUMED', 'ASSURED', 'ATTEMPT',
            'ATTRACT', 'AUCTION', 'AVERAGE', 'BACKING', 'BALANCE', 'BANKING', 'BARRIER', 'BATTERY', 'BEARING', 'BEATING',
            'BECAUSE', 'BEDROOM', 'BELIEVE', 'BENEATH', 'BENEFIT', 'BESIDES', 'BETWEEN', 'BILLION', 'BINDING', 'BLANKET',
            'BLESSED', 'BLOCKED', 'BOATING', 'BOOKING', 'BOOLEAN', 'BOUNCED', 'BOWLING', 'BRACKET', 'BROTHER', 'BUILDER',
            'BURNING', 'CABINET', 'CALIBER', 'CALLING', 'CAPABLE', 'CAPITAL', 'CAPTAIN', 'CAPTURE', 'CAREFUL', 'CARRIER',
            'CATALOG', 'CAUTION', 'CEILING', 'CENTRAL', 'CENTURY', 'CERTAIN', 'CHAMBER', 'CHANNEL', 'CHAPTER', 'CHARITY',
            'CHARLIE', 'CHARTER', 'CHECKED', 'CHICKEN', 'CIRCUIT', 'CLASSES', 'CLASSIC', 'CLEARED', 'CLIMATE', 'CLOTHES',
            'COLLEGE', 'COMBINE', 'COMFORT', 'COMMAND', 'COMMENT', 'COMPACT', 'COMPANY', 'COMPARE', 'COMPLEX', 'COMPOSE',
            'CONCEPT', 'CONCERN', 'CONCERT', 'CONDUCT', 'CONFIRM', 'CONNECT', 'CONSENT', 'CONSIST', 'CONTACT', 'CONTAIN',
            'CONTENT', 'CONTEST', 'CONTEXT', 'CONTROL', 'CONVERT', 'CORRECT', 'COUNCIL', 'COUNSEL', 'COUNTER', 'COUNTRY',
            'CRUCIAL', 'CRYSTAL', 'CULTURE', 'CURRENT', 'CUTTING', 'DECIDED', 'DECLARE', 'DECLINE', 'DEFAULT', 'DEFENCE',
            'DEFICIT', 'DELIVER', 'DENSITY', 'DEPOSIT', 'DESKTOP', 'DESPITE', 'DESTROY', 'DEVELOP', 'DEVICE', 'DIAMOND',
            'DIGITAL', 'DINNER', 'DISABLE', 'DISCUSS', 'DISEASE', 'DISPLAY', 'DISPUTE', 'DISTANT', 'DIVERSE', 'DIVIDED',
            'DRAWING', 'DRIVING', 'DYNAMIC', 'EARLIER', 'EASTERN', 'ECONOMY', 'EDITION', 'ELDERLY', 'ELEMENT', 'EMBASSY',
            'EMOTION', 'EMPEROR', 'EMPHASIS', 'ENABLE', 'ENCLAVE', 'ENDLESS', 'ENGAGED', 'ENHANCE', 'ENTERED', 'EPISODE',
            'EQUALLY', 'ESSENCE', 'EVENING', 'EVIDENT', 'EXACTLY', 'EXAMINE', 'EXAMPLE', 'EXCITED', 'EXCLUDE', 'EXHIBIT',
            'EXPENSE', 'EXPLAIN', 'EXPLORE', 'EXPRESS', 'EXTREME', 'FACTORY', 'FACULTY', 'FAILING', 'FAILURE', 'FASHION',
            'FEATURE', 'FEDERAL', 'FEELING', 'FICTION', 'FIFTEEN', 'FILMING', 'FINALLY', 'FINANCE', 'FINDING', 'FISHING',
            'FITNESS', 'FOREIGN', 'FOREVER', 'FORMULA', 'FORTUNE', 'FORWARD', 'FOUNDED', 'FOUNDER', 'FREEDOM', 'FURTHER',
            'GALLERY', 'GATEWAY', 'GENERAL', 'GENETIC', 'GENUINE', 'GIGABYT', 'GOURMET', 'GRADUAL', 'GRAMMAR', 'GRAVITY',
            'GREATER', 'GROWING', 'HABITAT', 'HALF-WAY', 'HAMBURG', 'HANDLED', 'HANGING', 'HARBOUR', 'HARMONY', 'HEADING',
            'HEALTHY', 'HEARING', 'HEAVILY', 'HELPFUL', 'HERITAGE', 'HIGHWAY', 'HIMSELF', 'HISTORY', 'HOLDING', 'HOLIDAY',
            'HONESTY', 'HOPEFUL', 'HORIZON', 'HOSPITAL', 'HOUSING', 'HOWEVER', 'HUNDRED', 'HURT-IN', 'HUSBAND', 'ILLEGAL',
            'ILLNESS', 'IMAGINE', 'IMMEDIAT', 'IMPROVE', 'INCLUDE', 'INITIAL', 'INSIGHT', 'INSTALL', 'INSTANT', 'INSTEAD',
            'INTENSE', 'INTERIM', 'INVOLVE', 'ISLANDS', 'ISOLATE', 'ISSUING', 'JACKSON', 'JOURNAL', 'JOURNEY', 'JUSTICE',
            'JUSTIFY', 'KEEPING', 'KILOMETER', 'KITCHEN', 'KNOWING', 'LANDING', 'LARGELY', 'LASTING', 'LATERAL', 'LAUNDRY',
            'LAWSUIT', 'LEADERS', 'LEADING', 'LEARNED', 'LEASING', 'LEISURE', 'LENDING', 'LESSONS', 'LIBERAL', 'LIBERTY',
            'LIBRARY', 'LICENSE', 'LIMITED', 'LISTING', 'LOGICAL', 'LOYALTY', 'LUGGAGE', 'MACHINE', 'MAGICAL', 'MAILING',
            'MAJESTY', 'MANAGING', 'MANDATE', 'MANKIND', 'MANSION', 'MARRIED', 'MASSIVE', 'MASTERY', 'MAXIMUM', 'MEANING',
            'MEASURE', 'MEDICAL', 'MEETING', 'MEMBERS', 'MENTION', 'MESSAGE', 'METHODS', 'MID-AIR', 'MIGHTY', 'MILLION',
            'MINERAL', 'MINIMAL', 'MINIMUM', 'MIRACLE', 'MISSILE', 'MISSION', 'MISTAKE', 'MIXTURE', 'MONITOR', 'MONTHLY',
            'MORNING', 'MUSICAL', 'MYSTERY', 'NATURAL', 'NEITHER', 'NERVOUS', 'NETWORK', 'NEUTRAL', 'NOMINEE', 'NOTHING',
            'NOWHERE', 'NUCLEAR', 'NUMBERS', 'NURSERY', 'NURSING', 'OBJECTS', 'OBVIOUS', 'OFFICER', 'ONGOING', 'OPENING',
            'OPERATE', 'OPINION', 'OPTICAL', 'OPTIMAL', 'ORGANIC', 'OUTCOME', 'OUTDOOR', 'OUTLOOK', 'OUTSIDE', 'OVERALL',
            'PACIFIC', 'PACKAGE', 'PAINTER', 'PARKING', 'PARTIAL', 'PARTNER', 'PASSAGE', 'PASSING', 'PASSION', 'PASSIVE',
            'PATIENT', 'PATTERN', 'PAYMENT', 'PENALTY', 'PENDING', 'PENSION', 'PERCENT', 'PERFECT', 'PERFORM', 'PHANTOM',
            'PHOENIX', 'PHYSICAL', 'PICTURE', 'PIONEER', 'PLASTIC', 'POETRY', 'POLITIC', 'POPULAR', 'PORTION', 'POSTURE',
            'POVERTY', 'PRAISED', 'PREDICT', 'PREMIER', 'PREMIUM', 'PREPARE', 'PRESENT', 'PREVENT', 'PRIMARY', 'PRINTER',
            'PRIVACY', 'PRIVATE', 'PROBLEM', 'PROCEED', 'PROCESS', 'PRODUCE', 'PRODUCT', 'PROFILE', 'PROGRAM', 'PROJECT',
            'PROMISE', 'PROMOTE', 'PROPOSAL', 'PROPOSE', 'PROTECT', 'PROTEST', 'PROUDFUL', 'PROVIDE', 'PROVOKE', 'PUBLISH',
            'PURPOSE', 'PURSUIT', 'QUALIFY', 'QUALITY', 'QUARTER', 'RADICAL', 'RAILWAY', 'RAPIDLY', 'READING', 'REALITY',
            'RECEIPT', 'RECEIVE', 'RECOVER', 'RECRUIT', 'REFLECT', 'REFORMS', 'REFUGEE', 'REFUSAL', 'REGULAR', 'RELEASE',
            'RELIANT', 'RELIEVE', 'REMAINS', 'REMOVAL', 'REPLACE', 'REPORTS', 'REQUEST', 'REQUIRE', 'RESERVE', 'RESOLVE',
            'RESPECT', 'RESPOND', 'RESTORE', 'RETIRED', 'REVENUE', 'REVERSE', 'ROUTINE', 'RUNNING', 'SATISFY', 'SAVINGS',
            'SCANNER', 'SCIENCE', 'SECTION', 'SEGMENT', 'SELLING', 'SENSING', 'SERIOUS', 'SERVICE', 'SESSION', 'SETTING',
            'SEVENTH', 'SEVERAL', 'SHARING', 'SHOCKED', 'SHOWING', 'SHUTTLE', 'SIGNALS', 'SILENCE', 'SIMILAR', 'SINCERE',
            'SIXTEEN', 'SMOKING', 'SOLDIER', 'SOMEHOW', 'SOMEONE', 'SPEAKER', 'SPECIAL', 'SPECIES', 'SPECIFY', 'SPONSOR',
            'STADIUM', 'STATION', 'STORAGE', 'STRANGE', 'SUBJECT', 'SUCCEED', 'SUCCESS', 'SUGGEST', 'SUMMARY', 'SUPPORT',
            'SUPPOSE', 'SUPREME', 'SURFACE', 'SURGEON', 'SURPLUS', 'SURVIVE', 'SUSPECT', 'SUSTAIN', 'SYMPTOM', 'SYSTEMS',
            'TALKING', 'TEACHER', 'TEENAGE', 'TENSION', 'TERRAIN', 'TESTING', 'TEXTURE', 'THEATRE', 'THERAPY', 'THOUGHT',
            'THROUGH', 'TONIGHT', 'TOTALLY', 'TOURISM', 'TOURIST', 'TRACKER', 'TRADING', 'TRAFFIC', 'TRAINED', 'TRAINER',
            'TRANSIT', 'TREATED', 'TRIGGER', 'TRIUMPH', 'TROUBLE', 'TRUSTEE', 'TYPICAL', 'ULTIMATE', 'UNAWARE', 'UNIFORM',
            'UNKNOWN', 'UNUSUAL', 'UPGRADE', 'UTILITY', 'VARIETY', 'VARIOUS', 'VEHICLE', 'VENTURE', 'VERSION', 'VICTORY',
            'VIEWING', 'VILLAGE', 'VIOLATE', 'VIRTUAL', 'VISIBLE', 'VISITOR', 'WALKING', 'WARNING', 'WARRANT', 'WARRIOR',
            'WEATHER', 'WEBCAST', 'WEEKDAY', 'WEEKEND', 'WELCOME', 'WESTERN', 'WHEREAS', 'WHEREBY', 'WHETHER', 'WILLING',
            'WINNING', 'WITHOUT', 'WITNESS', 'WORKING', 'WRITING', 'WRITTEN', 'YIELDED',
        ];

        // YB - 15-09-2026 Reset all words to non-targetable first, then mark curated vocabulary
        Word::query()->update(['is_targetable' => false]);

        $now = now();
        $records = [];

        foreach ($words5 as $w) {
            $records[] = [
                'word' => strtoupper(trim($w)),
                'length' => 5,
                'is_valid' => true,
                'is_targetable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($words6 as $w) {
            $records[] = [
                'word' => strtoupper(trim($w)),
                'length' => 6,
                'is_valid' => true,
                'is_targetable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($words7 as $w) {
            $records[] = [
                'word' => strtoupper(trim($w)),
                'length' => 7,
                'is_valid' => true,
                'is_targetable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Upsert in chunks to prevent memory or query parameter limits
        foreach (array_chunk($records, 100) as $chunk) {
            Word::upsert($chunk, ['word'], ['length', 'is_valid', 'is_targetable', 'updated_at']);
        }
    }
}
